<?php

namespace Gamegos\JWS\Algorithm;

class RSA_SSA_PKCSv15 implements AlgorithmInterface
{
    protected $sigAlgo;

    public function __construct($sigAlgo)
    {
        $this->sigAlgo = $sigAlgo;
    }

    /**
     * @param  string $key  PEM encoded private key
     * @param  mixed  $data
     * @return mixed
     */
    public function sign($key, $data)
    {
        $result = openssl_sign($data, $signature, $key, $this->sigAlgo);
        if (!$result) {
            throw new \RuntimeException(openssl_error_string());
        }

        return $signature;
    }

    /**
     * @param  string  $key       PEM encoded public key
     * @param  mixed   $data
     * @param  mixed   $signature
     * @return boolean
     */
    public function verify($key, $data, $signature)
    {
        return openssl_verify($data, $signature, $key, $this->sigAlgo) === 1;
    }
}
