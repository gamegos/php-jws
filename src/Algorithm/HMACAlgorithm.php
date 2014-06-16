<?php
namespace Gamegos\JWS\Algorithm;

class HMACAlgorithm implements AlgorithmInterface
{
    protected $hashAlgo;

    public function __construct($hashAlgo)
    {
        $this->hashAlgo = $hashAlgo;
    }

    public function sign($key, $data)
    {
        return hash_hmac($this->hashAlgo, $data, $key, true);
    }

    public function verify($key, $data, $signature)
    {
        return $this->sign($key, $data) === $signature;
    }
}
