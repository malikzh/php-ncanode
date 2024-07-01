<?php

namespace Malikzh\PhpNCANode;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Malikzh\PhpNCANode\Exceptions\ApiErrorException;
use Malikzh\PhpNCANode\Exceptions\InvalidResponseException;

abstract class Client
{
    /**
     * @var string
     */
    protected string $host = '';

    /**
     * @var int
     */
    protected int $timeout = 60;

    /**
     * @deprecated
     * @var array|string[]
     */
    protected array $baseRequest = [
        'version' => '3.0',
    ];

    /**
     * @param string $sNCANodeHost    Хост для подключения к NCANode
     * @param int    $iTimeoutSeconds Таймаут соединения
     */
    public function __construct(string $sNCANodeHost = 'http://127.0.0.1:14579', int $iTimeoutSeconds = 60)
    {
        $this->host    = $sNCANodeHost;
        $this->timeout = $iTimeoutSeconds;
    }

    /**
     * @param array $request
     *
     * @return array|mixed
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    protected function request(array $request): array|Exception
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
            'timeout'  => $this->timeout,
        ]);

        $endpoint = $this->host . $request['path'];
        $body = $request['params'];

        $result = $client->post($endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);

        $response = json_decode($result->getBody(), true);

        if (!$response) {
            throw new InvalidResponseException('Invalid response given: ' . var_export($result, true));
        }

        if ($result->getStatusCode() !== 200) {
            throw new ApiErrorException($response['message'], $response['status']);
        }

        return $response;
    }
}