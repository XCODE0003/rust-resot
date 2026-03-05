<?php

namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\HttpClients\HttpClientInterface;

/**
 * HTTP-клиент для Telegram Bot API.
 * Использует только синхронные запросы — обходит баг GuzzleHttpClient::__destruct()
 * с Promise\unwrap() в guzzlehttp/promises v2.
 */
class TelegramGuzzleHttpClient implements HttpClientInterface
{
    protected ClientInterface $client;
    protected int $timeOut = 30;
    protected int $connectTimeOut = 10;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
    }

    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false)
    {
        $body = $options['body'] ?? null;
        $requestOptions = [
            RequestOptions::HEADERS => $headers,
            RequestOptions::BODY => $body,
            RequestOptions::TIMEOUT => $this->getTimeOut(),
            RequestOptions::CONNECT_TIMEOUT => $this->getConnectTimeOut(),
        ];
        $requestOptions = array_merge($requestOptions, $options);

        try {
            return $this->client->request($method, $url, $requestOptions);
        } catch (GuzzleException $e) {
            $response = null;
            if ($e instanceof RequestExceptionInterface || $e instanceof RequestException) {
                $response = $e->getResponse();
            }
            if (!$response instanceof ResponseInterface) {
                throw new TelegramSDKException($e->getMessage(), $e->getCode(), $e);
            }
            return $response;
        }
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function setTimeOut($timeOut): self
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    public function getConnectTimeOut(): int
    {
        return $this->connectTimeOut;
    }

    public function setConnectTimeOut($connectTimeOut): self
    {
        $this->connectTimeOut = $connectTimeOut;
        return $this;
    }

}
