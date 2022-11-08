<?php

namespace Malikzh\PhpNCANode;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Malikzh\PhpNCANode\Exceptions\ApiErrorException;
use Malikzh\PhpNCANode\Exceptions\InvalidResponseException;

abstract class Client
{
    /**
     * @var string
     */
    protected string $host    = '';

    /**
     * @var int
     */
    protected int $timeout = 60;

    /**
     * @var array|string[]
     */
    protected array $baseRequest = [
        'version' => '2.0',
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
    protected function request(array $request)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
            'timeout'  => $this->timeout,
        ]);

        $result = $client->post($this->host, $request);

        $response = json_decode($result->getBody(), true);

        if (!$response) {
            throw new InvalidResponseException('Invalid response given: ' . var_export($result, true));
        }

        if (($response['status'] !== 0) || $result->getStatusCode() !== 200) {
            throw new ApiErrorException($response['message'], $response['status']);
        }

        return $response;
    }
}