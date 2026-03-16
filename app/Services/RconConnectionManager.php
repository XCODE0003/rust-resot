<?php

namespace App\Services;

use WebSocket\Client;
use App\Services\Socks5WebSocketClient;
use Illuminate\Support\Facades\Log;
use App\Models\Server;

class RconConnectionManager
{
    private static $instance = null;
    private $connections = [];
    private $lastActivity = [];
    private $reconnectAttempts = [];
    private $maxReconnectAttempts = 3;
    private $keepaliveInterval = 480;
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function connect($server_id)
    {
        $server = Server::find($server_id);
        if (!$server) {
            Log::channel('rcon_master')->error("Server not found: {$server_id}");
            return false;
        }
        
        $options = json_decode($server->options);
        $rcon_ip = $options->rcon_ip ?? '';
        $rcon_passw = $options->rcon_passw ?? '';
        
        if (empty($rcon_ip) || strpos($rcon_ip, '127.0.0.1') !== false) {
            Log::channel('rcon_master')->info("Invalid RCON IP for server {$server_id}");
            return false;
        }
        
        try {
            config(['server_api.ip' => $options->ip ?? '']);
            config(['server_api.rcon_ip' => $rcon_ip]);
            config(['server_api.api_url' => $options->api_url ?? '']);
            config(['server_api.api_key' => $options->api_key ?? '']);
            config(['server_api.rcon_passw' => $rcon_passw]);
            
            $wsUrl = "ws://{$rcon_ip}/{$rcon_passw}";
            $proxy = config('rcon.proxy', '');

            if (!empty($proxy)) {
                $this->connections[$server_id] = new Socks5WebSocketClient($wsUrl, [
                    'timeout' => 5,
                    'proxy' => $proxy,
                ]);
            } else {
                $this->connections[$server_id] = new Client($wsUrl);
            }

            $this->lastActivity[$server_id] = time();
            $this->reconnectAttempts[$server_id] = 0;
            
            Log::channel('rcon_master')->info("Connected to server {$server_id}");
            return true;
        } catch (\Exception $ex) {
            Log::channel('rcon_master')->error("Failed to connect to server {$server_id}: {$ex->getMessage()}");
            return false;
        }
    }
    
    public function getConnection($server_id)
    {
        if (!isset($this->connections[$server_id]) || !$this->isConnectionAlive($server_id)) {
            if (!isset($this->reconnectAttempts[$server_id])) {
                $this->reconnectAttempts[$server_id] = 0;
            }
            
            if ($this->reconnectAttempts[$server_id] >= $this->maxReconnectAttempts) {
                Log::channel('rcon_master')->warning("Max reconnect attempts reached for server {$server_id}");
                return null;
            }
            
            $this->reconnectAttempts[$server_id]++;
            $this->connect($server_id);
        }
        
        return $this->connections[$server_id] ?? null;
    }
    
    private function isConnectionAlive($server_id)
    {
        if (!isset($this->connections[$server_id])) {
            return false;
        }
        
        $inactiveTime = time() - ($this->lastActivity[$server_id] ?? 0);
        
        if ($inactiveTime > $this->keepaliveInterval && $inactiveTime < 540) {
            try {
                $data = json_encode([
                    'Identifier' => 0,
                    'Message'    => 'echo keepalive',
                    'Stacktrace' => '',
                    'Type'       => 3,
                ]);
                
                $this->connections[$server_id]->setTimeout(2);
                $this->connections[$server_id]->send($data);
                $this->connections[$server_id]->receive();
                
                $this->lastActivity[$server_id] = time();
                Log::channel('rcon_master')->info("Keepalive sent to server {$server_id}");
            } catch (\Exception $ex) {
                Log::channel('rcon_master')->warning("Keepalive failed for server {$server_id}: {$ex->getMessage()}");
                $this->disconnect($server_id);
                return false;
            }
        }
        
        if ($inactiveTime > 540) {
            Log::channel('rcon_master')->info("Connection inactive for {$inactiveTime}s, reconnecting server {$server_id}");
            $this->disconnect($server_id);
            return false;
        }
        
        return true;
    }
    
    public function sendCommand($server_id, $command, $timeout = 5)
    {
        $connection = $this->getConnection($server_id);
        
        if (!$connection) {
            Log::channel('rcon_master')->error("No connection available for server {$server_id}");
            return false;
        }
        
        try {
            $connection->setTimeout($timeout);
            
            $data = json_encode([
                'Identifier' => 0,
                'Message'    => $command,
                'Stacktrace' => '',
                'Type'       => 3,
            ]);
            
            $connection->send($data);
            $result = json_decode($connection->receive());
            
            $this->lastActivity[$server_id] = time();
            $this->reconnectAttempts[$server_id] = 0;
            
            Log::channel('rcon_master')->info("Command sent to server {$server_id}: {$command}");
            
            return $result;
        } catch (\Exception $ex) {
            Log::channel('rcon_master')->error("Failed to send command to server {$server_id}: {$ex->getMessage()}");
            $this->disconnect($server_id);
            return false;
        }
    }
    
    public function disconnect($server_id)
    {
        if (isset($this->connections[$server_id])) {
            try {
                $this->connections[$server_id]->close();
            } catch (\Exception $ex) {
                Log::channel('rcon_master')->warning("Error closing connection for server {$server_id}: {$ex->getMessage()}");
            }
            unset($this->connections[$server_id]);
            unset($this->lastActivity[$server_id]);
        }
    }
    
    public function disconnectAll()
    {
        foreach (array_keys($this->connections) as $server_id) {
            $this->disconnect($server_id);
        }
    }
    
    public function reconnectIfNeeded($server_id)
    {
        if (!$this->isConnectionAlive($server_id)) {
            return $this->connect($server_id);
        }
        return true;
    }
}
