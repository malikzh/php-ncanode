<?php

namespace Malikzh\PhpNCANode;

/**
 * Class XMLVerificationResult
 *
 * Класс информации о проверки XML-подписи
 *
 * @package Malikzh\PhpNCANode
 */
class XMLVerificationResult
{
    protected $result = null;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * Возвращает "сырые" данные, иными словами, то что вернул сервер
     *
     * @return array|null
     */
    public function getRaw() {
        return $this->result;
    }

    /**
     * Является ли серификат действителен по сроку (срок проверяется на стороне NCANode)
     *
     * @return mixed
     */
    public function isValid() {
        return $this->result['valid'];
    }

    /**
     * Возвращает сертфикат, при его присутствии
     *
     * @return CertificateInfo|null
     */
    public function getCert() {
        return (isset($this->result['cert']) && $this->result['cert'] !== null ? (new CertificateInfo($this->result['cert'])) : null);
    }
}