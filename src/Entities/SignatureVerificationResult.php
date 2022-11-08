<?php

namespace Malikzh\PhpNCANode\Entities;

class SignatureVerificationResult
{
    /**
     * @var array
     */
    protected array $signatureResult;

    /**
     * @param array $signatureResult
     */
    public function __construct(array $signatureResult)
    {
        $this->signatureResult = $signatureResult;
    }

    /**
     * @return array
     */
    public function raw(): array
    {
        return $this->signatureResult;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->signatureResult['signers'] as $signer) {
            if ($signer['cert']['valid'] === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function signers()
    {
        return $this->signatureResult['signers'];
    }

    /**
     * @return Certificate[]
     *
     * @throws \Exception
     */
    public function certs(): array
    {
        $certificates = [];

        foreach ($this->signatureResult['signers'] as $signer) {
            $certificates[] = new Certificate(array_merge($signer['cert'], ['chain' => $signer['chain']]));
        }

        return $certificates;
    }
}