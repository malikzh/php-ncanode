<?php

namespace Malikzh\PhpNCANode;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Malikzh\PhpNCANode\Entities\Certificate;
use Malikzh\PhpNCANode\Entities\SignatureVerificationResult;
use Malikzh\PhpNCANode\Exceptions\ApiErrorException;
use Malikzh\PhpNCANode\Exceptions\InvalidResponseException;

/**
 * Класс для работы с API NCANode 3 версии
 *
 * @package Malikzh\PhpNCANode
 * @author Malik Zharykov <cmalikz.h@gmail.com>
 * @author Rustem Kaimolla <rustem@exact.kz>
 *
 * @contributor Rakhat Bakytzhanov <singlephon@gmail.com>
 */
class NCANodeClient extends Client
{
    /**
     * @throws InvalidResponseException
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws Exception
     */
    public function pkcs12Info(
        string $p12Base64,
        string $sPassword,
        array  $revocationCheck = ['OCSP'],
        string $alias = null
    ): Certificate
    {
        $request = [
            'path' => '/pkcs12/info',
            'params' => [
                'revocationCheck' => $revocationCheck,
                'keys' => [
                    [
                        'key' => $p12Base64,
                        'password'  => $sPassword,
                        'keyAlias'  => $alias
                    ]
                ],
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return new Certificate($response['signers'][0]);
    }

    /**
     * @param array $p12s
     * @param array $revocationCheck
     * @param string|null $alias
     * @return array
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     * @throws Exception
     */
    public function pkcs12InfoBulk(
        array $p12s,
        array  $revocationCheck = ['OCSP'],
        string $alias = null
    ): array
    {
        $request = [
            'path' => '/pkcs12/info',
            'params' => [
                'revocationCheck' => $revocationCheck,
                'keys' => iterator($p12s, function ($key, $password) use ($alias) {
                    return [
                        'key' => $key,
                        'password'  => $password,
                        'keyAlias'  => $alias
                    ];
                }, true),
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return iterator($response['signers'], function (array $signer) {
            return new Certificate($signer);
        });
    }


    /**
     * @param string $p12Base64
     * @param string $sPassword
     * @param string|null $alias
     * @return array|null
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function pkcs12AliasInfo(
        string $p12Base64,
        string $sPassword,
        string $alias = null
    ): array|null
    {
        $request = [
            'path' => '/pkcs12/aliases',
            'params' => [
                'keys' => [
                    [
                        'key' => $p12Base64,
                        'password'  => $sPassword,
                        'keyAlias'  => $alias
                    ]
                ],
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return @ $response['aliases'][0];
    }

    /**
     * @param array $p12s
     * @param string|null $alias
     * @return array|null
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function pkcs12AliasesInfoBulk(
        array $p12s,
        string $alias = null
    ): array|null
    {
        $request = [
            'path' => '/pkcs12/aliases',
            'params' => [
                'keys' => iterator($p12s, function ($key, $password) use ($alias) {
                    return [
                        'key' => $key,
                        'password'  => $password,
                        'keyAlias'  => $alias
                    ];
                }, true),
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return @ $response['aliases'];
    }


    /**
     * @param string $base64data
     * @param string $p12Base64
     * @param string $sPassword
     * @param string|null $alias
     * @param bool $withTsp
     * @param string $tsaPolicy
     * @param bool $detached
     * @return string|Exception
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function cmsSign(string $base64data, string $p12Base64, string $sPassword, string $alias = null, bool $withTsp = true, string $tsaPolicy = 'TSA_GOST_POLICY', bool $detached = false): string|Exception
    {
        $request = [
            'path' => '/cms/sign',
            'params' => [
                'data' => $base64data,
                'signers' => [
                    [
                        'key' => $p12Base64,
                        'password'  => $sPassword,
                        'keyAlias'  => $alias
                    ]
                ],
                'withTsp' => $withTsp,
                'tsaPolicy' => $tsaPolicy,
                'detached' => $detached,
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return $response['cms'];
    }

    /**
     * @param string $base64data
     * @param array $p12s
     * @param string|null $alias
     * @param bool $withTsp
     * @param string $tsaPolicy
     * @param bool $detached
     * @return string|Exception
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function cmsBulkSign(string $base64data, array $p12s, string $alias = null, bool $withTsp = true, string $tsaPolicy = 'TSA_GOST_POLICY', bool $detached = false): string|Exception
    {
        $request = [
            'path' => '/cms/sign',
            'params' => [
                'data' => $base64data,
                'signers' => iterator($p12s, function ($key, $password) use ($alias) {
                    return [
                        'key' => $key,
                        'password'  => $password,
                        'keyAlias'  => $alias
                    ];
                }, true),
                'withTsp' => $withTsp,
                'tsaPolicy' => $tsaPolicy,
                'detached' => $detached,
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return $response['cms'];
    }


    /**
     * @param string $base64data
     * @param string $cms
     * @param string $p12Base64
     * @param string $sPassword
     * @param string|null $alias
     * @param bool $withTsp
     * @param string $tsaPolicy
     * @param bool $detached
     * @return mixed
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function cmsSignAdd(string $base64data, string $cms, string $p12Base64, string $sPassword, string $alias = null, bool $withTsp = true, string $tsaPolicy = 'TSA_GOST_POLICY', bool $detached = false)
    {
        $request = [
            'path' => '/cms/sign/add',
            'params' => [
                'data' => $base64data,
                'cms' => $cms,
                'signers' => [
                    [
                        'key' => $p12Base64,
                        'password'  => $sPassword,
                        'keyAlias'  => $alias
                    ]
                ],
                'withTsp' => $withTsp,
                'tsaPolicy' => $tsaPolicy,
                'detached' => $detached,
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return $response['cms'];
    }




    /**
     * @param string $base64data
     * @param string $cms
     * @param array $revocationCheck
     * @return SignatureVerificationResult
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public function cmsVerify(
        string $base64data,
        string $cms,
        array  $revocationCheck = ['OCSP'],
    ): SignatureVerificationResult
    {
        $request = [
            'path' => '/cms/verify',
            'params' => [
                'revocationCheck' => $revocationCheck,
                'cms' => $cms,
                'withTsp' => true,
                'data' => $base64data
            ]
        ];

        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return new SignatureVerificationResult($response);
    }


    /**
     * @throws InvalidResponseException
     * @throws ApiErrorException
     * @throws GuzzleException
     */
    public function cmsExtract(string $cms)
    {
        $request = [
            'path' => '/cms/extract',
            'params' => [
                'cms' => $cms,
            ]
        ];
        $response = $this->request($request);

        if ($response['status'] !== 200) {
            throw new InvalidResponseException($response['message']);
        }

        return $response['data'];
    }
}
