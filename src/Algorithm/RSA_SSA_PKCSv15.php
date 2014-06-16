<?php
/**
 * Created by PhpStorm.
 * User: mustafa
 * Date: 6/16/14
 * Time: 10:34 AM
 */

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
        openssl_sign($data, $signature, $key, $this->sigAlgo);

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
