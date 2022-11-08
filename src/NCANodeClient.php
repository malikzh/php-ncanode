<?php

namespace Malikzh\PhpNCANode;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Malikzh\PhpNCANode\Entities\Certificate;
use Malikzh\PhpNCANode\Entities\SignatureVerificationResult;
use Malikzh\PhpNCANode\Exceptions\ApiErrorException;
use Malikzh\PhpNCANode\Exceptions\InvalidResponseException;

/**
 * Класс для работы с API NCANode 2 версии
 *
 * @package Malikzh\PhpNCANode
 * @author Malik Zharykov <cmalikz.h@gmail.com>
 * @author Rustem Kaimolla <rustem@exact.kz>
 */
class NCANodeClient extends Client
{
    /**
     * @return array
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function nodeInfo()
    {
        return $this->request(
            array_merge(
                $this->baseRequest,
                [
                    'method' => 'node.info',
                ]
            )
        );
    }

    /**
     * @param string $p12Base64
     * @param string $sPassword
     * @param bool $checkOcsp
     * @param bool $checkCrl
     * @param string $alias
     *
     * @return Certificate
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws Exception
     * @throws GuzzleException
     */
    public function pkcs12Info(
        string $p12Base64,
        string $sPassword,
        bool   $checkOcsp = true,
        bool   $checkCrl  = true,
        string $alias     = ""): Certificate
    {
        $request = array_merge($this->baseRequest, [
            'method' => 'info.pkcs12',
            'params' => [
                'p12'       => $p12Base64,
                'password'  => $sPassword,
                'checkOcsp' => $checkOcsp,
                'checkCrl'  => $checkCrl,
                'alias'     => $alias
            ]
        ]);

        $response = $this->request($request);

        if ($response['status'] !== 0) {
            throw new InvalidResponseException($response['message']);
        }

        return new Certificate($response['certificate']);
    }

    /**
     * @param string $p12Base64
     * @param string $sPassword
     *
     * @return mixed
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function pkcs12AliasesInfo(string $p12Base64, string $sPassword)
    {
        $request = array_merge($this->baseRequest, [
            'method' => 'info.pkcs12',
            'params' => [
                'p12'      => $p12Base64,
                'password' => $sPassword,
            ]
        ]);

        $response = $this->request($request);

        if ($response['status'] !== 0) {
            throw new InvalidResponseException($response['message']);
        }

        return $response['aliases'];
    }

    /**
     * @param string $base64data
     * @param string $p12Base64
     * @param string $sPassword
     * @param string $alias
     * @param bool $withTsp
     *
     * @return mixed
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function cmsSign(string $base64data, string $p12Base64, string $sPassword, string $alias = "", bool $withTsp = false)
    {
        $request = array_merge($this->baseRequest, [
            'method' => 'cms.sign',
            'params' => [
                'data'     => $base64data,
                'withTsp'  => $withTsp,
                'p12array' => [
                    [
                        'alias'    => $alias,
                        'p12'      => $p12Base64,
                        'password' => $sPassword,
                    ]
                ],
            ]
        ]);

        $response = $this->request($request);

        if ($response['status'] !== 0) {
            throw new InvalidResponseException($response['message']);
        }

        return new $response['cms'];
    }


    /**
     * @param string $base64data
     * @param bool $checkOcsp
     * @param bool $checkCrl
     *
     * @return SignatureVerificationResult
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function cmsVerify(string $base64data, bool $checkOcsp = true, bool $checkCrl = true)
    {
        $request = array_merge($this->baseRequest, [
            'method' => 'cms.verify',
            'params' => [
                'checkOcsp' => $checkOcsp,
                'checkCrl'  => $checkCrl,
                'cms'       => $base64data,
            ]
        ]);

        $response = $this->request($request);

        if ($response['status'] !== 0) {
            throw new InvalidResponseException($response['message']);
        }

        return new SignatureVerificationResult($response['result']);
    }

    /**
     * @param string $cms
     *
     * @return mixed
     *
     * @throws ApiErrorException
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function cmsExtract(string $cms)
    {
        $request = array_merge($this->baseRequest, ['method' => 'cms.extract', 'params' => ['cms' => $cms]]);

        $response = $this->request($request);

        if ($response['status'] !== 0) {
            throw new InvalidResponseException($response['message']);
        }

        return new $response['originalData'];
    }
}
