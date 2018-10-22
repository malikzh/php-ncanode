<?php

namespace Malikzh\PhpNCANode;

/**
 * Class NCANodeClient
 *
 * Класс для работы с API NCANode
 *
 * @package Malikzh\PhpNCANode
 * @author Malik Zharykov <cmalikz.h@gmail.com>
 */
class NCANodeClient
{
    protected $host = '';
    protected $timeout = 60;

    /**
     * NCANodeClient constructor.
     *
     * @param string $sNCANodeHost Хост для подключения к NCANode
     * @param int $iTimeoutSeconds Таймаут соединения
     */
    public function __construct($sNCANodeHost = 'http://127.0.0.1:14579', $iTimeoutSeconds = 60)
    {
        $this->host    = $sNCANodeHost;
        $this->timeout = $iTimeoutSeconds;
    }

    /**
     * Возвращает информацию о сервере NCANode
     *
     * @return mixed Массив с информацией
     * @throws ApiErrorException Если произошла ошибка со стороны API (баги например) или status != 0, то будет брошено это исключение
     * @throws CurlException Ошибка со стороны cURL
     * @throws InvalidResponseException Ошибка, когда получили неверный ответ от сервера (не в формате JSON)
     * @throws NCANodeException Не удалось проинциализорвать cURL
     */
    public function nodeInfo()
    {
        $request = [
            'version' => '1.0',
            'method'  => 'NODE.info'
        ];

        $result = $this->request($request);
        return $result['result'];
    }

    /**
     * Возвращает информацию о P12-файле
     *
     * @param string $p12Base64 Закодированный в Base64 файл P12
     * @param string $sPassword Пароль к файлу
     * @param bool $bVerifyOcsp Производить проверку по OCSP?
     * @param bool $bVerifyCrl Производить проверку по CRL?
     * @return CertificateInfo Информация о сертификате
     * @throws ApiErrorException Произошла ошибка со стороны API. Неверный сертификат, неверный пароль и т.д.
     * @throws CurlException Произошла ошибка со стороны cURL
     * @throws InvalidResponseException Ошибка, когда получили неверный ответ от сервера (не в формате JSON)
     * @throws NCANodeException Не удалось проинциализорвать cURL
     */
    public function pkcs12Info($p12Base64, $sPassword, $bVerifyOcsp = false, $bVerifyCrl = false)
    {
        $request = [
            'version' => '1.0',
            'method'  => 'PKCS12.info',
            'params'  => [
                'p12'        => (string)$p12Base64,
                'password'   => (string)$sPassword,
                'verifyOcsp' => (bool)$bVerifyOcsp,
                'verifyCrl'  => (bool)$bVerifyCrl
            ]
        ];

        $result = $this->request($request);
        return new CertificateInfo($result['result']);
    }

    /**
     * Возвращает информацию о X509 сертификате
     *
     * @param string $x509Base64 Закодированный в Base64 X509 сертификат
     * @param bool $bVerifyOcsp Производить проверку по OCSP?
     * @param bool $bVerifyCrl Производить проверку по CRL?
     * @return CertificateInfo Информация о сертификате
     * @throws ApiErrorException Произошла ошибка со стороны API. Неверный сертификат, неверный пароль и т.д.
     * @throws CurlException Произошла ошибка со стороны cURL
     * @throws InvalidResponseException Ошибка, когда получили неверный ответ от сервера (не в формате JSON)
     * @throws NCANodeException Не удалось проинциализорвать cURL
     */
    public function x509Info($x509Base64, $bVerifyOcsp = false, $bVerifyCrl = false)
    {
        $request = [
            'version' => '1.0',
            'method'  => 'X509.info',
            'params'  => [
                'cert'        => (string)$x509Base64,
                'verifyOcsp' => (bool)$bVerifyOcsp,
                'verifyCrl'  => (bool)$bVerifyCrl
            ]
        ];

        $result = $this->request($request);
        return new CertificateInfo($result['result']);
    }


    /**
     * Подписывает XML
     *
     * @param string $sXml XML данные, которые надо подписать
     * @param string $p12Base64 Закодированный в Base64, файл P12
     * @param string$sPassword Пароль к файлу p12
     * @return mixed Результат подписания
     * @throws ApiErrorException Произошла ошибка со стороны API. Неверный сертификат, неверный пароль и т.д.
     * @throws CurlException Произошла ошибка со стороны cURL
     * @throws InvalidResponseException Ошибка, когда получили неверный ответ от сервера (не в формате JSON)
     * @throws NCANodeException Не удалось проинциализорвать cURL
     */
    public function xmlSign($sXml, $p12Base64, $sPassword)
    {
        $request = [
            'version' => '1.0',
            'method'  => 'XML.sign',
            'params'  => [
                'xml'        => (string)$sXml,
                'p12'        => (string)$p12Base64,
                'password'   => (string)$sPassword,
            ]
        ];

        $result = $this->request($request);
        return $result['result'];
    }

    /**
     * Проверяет XML подпись
     *
     * @param string $sXml XML данные, которые надо подписать
     * @param bool $bVerifyOcsp Производить проверку по OCSP?
     * @param bool $bVerifyCrl Производить проверку по CRL?
     * @return XMLVerificationResult Результат проверки. Также содержит сертификат
     * @throws ApiErrorException Произошла ошибка со стороны API. Неверный сертификат, неверный пароль и т.д.
     * @throws CurlException Произошла ошибка со стороны cURL
     * @throws InvalidResponseException Ошибка, когда получили неверный ответ от сервера (не в формате JSON)
     * @throws NCANodeException Не удалось проинциализорвать cURL
     */
    public function xmlVerify($sXml, $bVerifyOcsp = false, $bVerifyCrl = false)
    {
        $request = [
            'version' => '1.0',
            'method'  => 'XML.verify',
            'params'  => [
                'xml'        => (string)$sXml,
                'verifyOcsp' => (bool)$bVerifyOcsp,
                'verifyCrl'  => (bool)$bVerifyCrl
            ]
        ];

        $result = $this->request($request);
        return new XMLVerificationResult($result['result']);
    }

    protected function request(array $requestJSON)
    {
        $ch = curl_init($this->host);

        if ($ch == false) {
            throw new NCANodeException(curl_error($ch), curl_errno($ch));
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($requestJSON),
            CURLOPT_TIMEOUT        => $this->timeout
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch) !== CURLE_OK) {
            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        curl_close($ch);


        $resultJson = json_decode($result, true);

        if (!$resultJson) {
            throw new InvalidResponseException('Invalid response given: ' . var_export($result, true), json_last_error());
        }

        if ($resultJson['status'] !== 0) {
            throw new ApiErrorException($resultJson['message'], $resultJson['status']);
        }

        unset($resultJson['message'], $resultJson['status']);

        return $resultJson;
    }
}