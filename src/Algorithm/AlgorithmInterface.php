<?php
namespace Gamegos\JWS\Algorithm;

interface AlgorithmInterface
{
    /**
     * @param  mixed  $key
     * @param  string $data
     * @return mixed
     */
    public function sign($key, $data);

    /**
     * @param  mixed   $key
     * @param  string  $data
     * @param  mixed   $signature
     * @return boolean
     */
    public function verify($key, $data, $signature);
}
