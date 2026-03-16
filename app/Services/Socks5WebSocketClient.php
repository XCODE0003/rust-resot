<?php

namespace App\Services;

use PhpSocks\Client as PhpSocksClient;
use WebSocket\Connection;
use WebSocket\ConnectionException;
use WebSocket\Message\Factory as MessageFactory;
use Psr\Log\NullLogger;

/**
 * WebSocket клиент через SOCKS5 прокси.
 * Совместим с WebSocket\Client по интерфейсу (setTimeout, send, receive, close).
 */
class Socks5WebSocketClient
{
    private ?Connection $connection = null;
    private string $wsUri;
    private array $options;
    private array $proxyConfig;

    /**
     * @param string $uri WebSocket URI (ws://host:port/path)
     * @param array $options timeout, proxy (host:port:user:pass)
     */
    public function __construct(string $uri, array $options = [])
    {
        $this->wsUri = $uri;
        $this->options = array_merge(['timeout' => 5], $options);
        $this->proxyConfig = $this->parseProxyConfig($options['proxy'] ?? '');
    }

    public function setTimeout(int $seconds): void
    {
        $this->options['timeout'] = $seconds;
        if ($this->connection) {
            $this->connection->setTimeout($seconds);
        }
    }

    /**
     * @param string $payload JSON или текст для отправки
     */
    public function send(string $payload): void
    {
        $this->ensureConnected();
        $factory = new MessageFactory();
        $message = $factory->create('text', $payload);
        $this->connection->pushMessage($message, true);
    }

    /**
     * @return string|null Ответ от сервера
     */
    public function receive()
    {
        $this->ensureConnected();
        while (true) {
            $message = $this->connection->pullMessage();
            $opcode = $message->getOpcode();
            if (in_array($opcode, ['text', 'binary'])) {
                return $message->getContent();
            }
            if ($opcode === 'close') {
                return null;
            }
        }
    }

    public function close(int $status = 1000, string $msg = 'ttfn'): void
    {
        if ($this->connection && $this->connection->isConnected()) {
            $this->connection->close($status, $msg);
        }
    }

    private function ensureConnected(): void
    {
        if ($this->connection && $this->connection->isConnected()) {
            return;
        }
        $this->connect();
    }

    private function connect(): void
    {
        if (empty($this->proxyConfig)) {
            throw new ConnectionException('SOCKS5 proxy not configured');
        }

        $parsed = parse_url($this->wsUri);
        if (!$parsed || !isset($parsed['host'])) {
            throw new ConnectionException('Invalid WebSocket URI: ' . $this->wsUri);
        }

        $host = $parsed['host'];
        $port = $parsed['port'] ?? 80;
        $path = $parsed['path'] ?? '/';
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $socksClient = new PhpSocksClient([
            'host' => $this->proxyConfig['host'],
            'port' => $this->proxyConfig['port'],
            'connect_timeout' => (float) $this->options['timeout'],
            'timeout' => $this->options['timeout'],
            'auth' => $this->proxyConfig['auth'] ?? [],
        ]);

        $stream = $socksClient->connect("tcp://{$host}:{$port}");
        $socket = $this->getStreamResource($stream);

        $this->connection = new Connection($socket, $this->options);
        $this->connection->setLogger(new NullLogger());
        $this->connection->setTimeout($this->options['timeout']);

        $key = $this->generateKey();
        $hostHeader = $host . (($port != 80 && $port != 443) ? ":{$port}" : '');

        $headers = [
            'Host' => $hostHeader,
            'User-Agent' => 'websocket-client-php',
            'Connection' => 'Upgrade',
            'Upgrade' => 'websocket',
            'Sec-WebSocket-Key' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        $header = "GET {$path} HTTP/1.1\r\n" . implode(
            "\r\n",
            array_map(fn($k, $v) => "{$k}: {$v}", array_keys($headers), $headers)
        ) . "\r\n\r\n";

        $this->connection->write($header);

        $response = '';
        do {
            $buffer = $this->connection->gets(1024);
            $response .= $buffer;
        } while (strpos($response, "\r\n\r\n") === false);

        if (!preg_match('#Sec-WebSocket-Accept:\s*(.*)$#mUi', $response, $matches)) {
            throw new ConnectionException('Invalid upgrade response: ' . substr($response, 0, 200));
        }

        $expected = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        if (trim($matches[1]) !== $expected) {
            throw new ConnectionException('Server sent bad upgrade response.');
        }
    }

    private function generateKey(): string
    {
        $key = '';
        for ($i = 0; $i < 16; $i++) {
            $key .= chr(rand(33, 126));
        }
        return base64_encode($key);
    }

    private function parseProxyConfig(string $proxy): array
    {
        $proxy = trim($proxy);
        if (empty($proxy)) {
            return [];
        }

        $parts = explode(':', $proxy, 4);
        if (count($parts) < 2) {
            return [];
        }

        $config = [
            'host' => $parts[0],
            'port' => (int) $parts[1],
            'auth' => [],
        ];

        if (count($parts) >= 4) {
            $config['auth'] = [
                'username' => $parts[2],
                'password' => $parts[3],
            ];
        }

        return $config;
    }

    private function getStreamResource($stream)
    {
        if (is_resource($stream)) {
            return $stream;
        }
        $ref = new \ReflectionClass($stream);
        $prop = $ref->getProperty('sock');
        $prop->setAccessible(true);
        $sock = $prop->getValue($stream);
        $prop->setAccessible(false);
        return $sock;
    }
}
