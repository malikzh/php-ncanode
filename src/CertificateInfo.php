<?php
namespace Malikzh\PhpNCANode;

/**
 * Class CertificateInfo
 *
 * Класс информации о сертификате
 *
 * @package Malikzh\PhpNCANode
 */
class CertificateInfo
{
    protected $cert = [];
    public $notBefore = null;
    public $notAfter  = null;
    public $chain = [];

    public function __construct(array $certInfo)
    {
        $this->cert = $certInfo;
        $this->notBefore = new \DateTime($certInfo['notBefore']);
        $this->notAfter = new \DateTime($certInfo['notAfter']);

        if (isset($certInfo['chain']) && is_array($certInfo['chain'])) {
            foreach ($certInfo['chain'] as $c) {
                $this->chain[] = new self($c);
            }
        }
    }

    public function getRaw() {
        return $this->cert;
    }

    public function __get($name)
    {
        return $this->cert[$name];
    }

    public function __set($name, $value)
    {
        throw new \Exception('Certificate arguments setting not supported.');
    }

    public function __isset($name)
    {
        return isset($this->cert[$name]);
    }

    public function __unset($name)
    {
        throw new \Exception('CertificateInfo unset not supported');
    }

    /**
     * Проверяет, действителен ли сертификат на заданную дату?
     *
     * @param null $oDate Дата, если не указать, то берется текущее время
     * @return bool Результат проверки
     */
    public function isExpired($oDate = null) {
        if (!$oDate) {
            $oDate = new \DateTime('now');
        }

        return $this->notBefore > $oDate && $this->notAfter < $oDate;
    }

    /**
     * Проверяет, является ли сертификат законным для подписания?
     *
     * Здесь проверяется, срок (на стороне сервера), ocsp и crl (при указании) и цепочка, до КУЦ
     *
     * Для полноты проверки рекомендуется verifyOcsp или verifyCrl указывать в true
     *
     * @param bool $bCheckChain Проверять цепочку
     * @return bool
     */
    public function isLegal($bCheckChain = true) {

        // check date
        if (!$this->cert['valid']) return false;

        // check ocsp if present
        if (isset($this->cert['ocsp']) && $this->cert['ocsp']['status'] !== 'ACTIVE') return false;

        // check crl if present
        if (isset($this->cert['crl']) && $this->cert['crl']['status'] !== 'ACTIVE') return false;

        // check chain
        if ($bCheckChain) {
            if (!isset($this->cert['chain']) || !is_array($this->cert['chain']) || count($this->cert['chain']) == 0) {
                return false;
            } else {
                foreach ($this->chain as $chainCert) {
                    if (!$chainCert->isLegal(false)) return false;
                }
            }
        }

        return true;
    }
}