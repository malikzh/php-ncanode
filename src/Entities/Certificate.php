<?php

namespace Malikzh\PhpNCANode\Entities;

use DateTime;

class Certificate
{
    protected array $certificate = [];
    public DateTime $notBefore;
    public DateTime $notAfter;
    public array    $chains = [];

    /**
     * @throws \Exception
     */
    public function __construct(array $certificate)
    {
        $this->certificate = $certificate;
        $this->notBefore = new DateTime($certificate['notBefore']);
        $this->notAfter  = new DateTime($certificate['notAfter']);

        if (isset($certificate['chain']) && is_array($certificate['chain'])) {
            foreach ($certificate['chain'] as $chain) {
                $this->chains[] = new self($chain);
            }
        }
    }

    /**
     * @return array
     */
    public function raw(): array
    {
        return $this->certificate;
    }

    public function __get($name)
    {
        return $this->certificate[$name];
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
     *
     * @return bool Результат проверки
     */
    public function isExpired($oDate = null): bool
    {
        if (!$oDate) {
            $oDate = new DateTime('now');
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
     *
     * @return bool
     */
    public function isLegal(bool $bCheckChain = true): bool
    {
        // check date
        if (!$this->certificate['valid']) {
            return false;
        }

        // check ocsp if present
        if (isset($this->certificate['ocsp']) && $this->certificate['ocsp']['status'] !== 'ACTIVE') {
            return false;
        }

        // check crl if present
        if (isset($this->certificate['crl']) && $this->certificate['crl']['status'] !== 'ACTIVE') {
            return false;
        }

        // check chain
        if ($bCheckChain) {
            if (
                !isset($this->certificate['chain']) ||
                !is_array($this->certificate['chain']) ||
                count($this->certificate['chain']) == 0
            ) {
                return false;
            } else {
                foreach ($this->chains as $chainCert) {
                    if (!$chainCert->isLegal(false)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
