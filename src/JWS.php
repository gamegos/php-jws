<?php
namespace Gamegos\JWS;

use Gamegos\JWS\Algorithm\AlgorithmInterface;
use Gamegos\JWS\Algorithm\HMACAlgorithm;
use Gamegos\JWS\Algorithm\RSA_SSA_PKCSv15;
use Gamegos\JWS\Exception\InvalidSignatureException;
use Gamegos\JWS\Exception\MalformedSignatureException;
use Gamegos\JWS\Exception\UnspecifiedAlgorithmException;
use Gamegos\JWS\Exception\UnsupportedAlgorithmException;
use Gamegos\JWS\Util\Base64Url;
use Gamegos\JWS\Util\Json;

class JWS
{
    protected $algorithms = array();

    /**
     * @param AlgorithmInterface[] $algorithms
     */
    public function __construct($algorithms = null)
    {
        if (is_array($algorithms)) {
            $this->registerAlgorithms($algorithms);
        } else {
            //built-in algorithms
            $this->registerAlgorithm('HS256', new HMACAlgorithm('sha256'));
            $this->registerAlgorithm('HS384', new HMACAlgorithm('sha384'));
            $this->registerAlgorithm('HS512', new HMACAlgorithm('sha512'));

            $this->registerAlgorithm('RS256', new RSA_SSA_PKCSv15(OPENSSL_ALGO_SHA256));
            $this->registerAlgorithm('RS384', new RSA_SSA_PKCSv15(OPENSSL_ALGO_SHA384));
            $this->registerAlgorithm('RS512', new RSA_SSA_PKCSv15(OPENSSL_ALGO_SHA512));
        }
    }

    /**
     * @param AlgorithmInterface[] $algorithms
     */
    public function registerAlgorithms(array $algorithms)
    {
        foreach ($algorithms as $name => $algorithm) {
            $this->registerAlgorithm($name, $algorithm);
        }
    }

    /**
     * @param string             $name
     * @param AlgorithmInterface $algorithm
     */
    public function registerAlgorithm($name, AlgorithmInterface $algorithm)
    {
        $this->algorithms[$name] = $algorithm;
    }

    /**
     * @return array
     */
    public function supportedAlgorithms()
    {
        return array_keys($this->algorithms);
    }

    /**
     * @param $name
     * @return AlgorithmInterface
     */
    private function _getAlgorithm($name)
    {
        if (!isset($this->algorithms[$name])) {
            throw new UnsupportedAlgorithmException(sprintf("Signing algorithm '%s' is not supported", $name));
        }

        return $this->algorithms[$name];
    }

    /**
     * @param  array  $headers
     * @param  mixed  $payload
     * @param         $key
     * @return string
     */
    public function encode(array $headers, $payload, $key)
    {
        if (empty($headers['alg'])) {
            throw new UnspecifiedAlgorithmException("'alg' header parameter is required.");
        }

        $algorithm = $this->_getAlgorithm($headers['alg']);

        $headerComponent  = Base64Url::encode(Json::encode($headers));
        $payloadComponent = Base64Url::encode(Json::encode($payload));

        $dataToSign = $headerComponent.'.'.$payloadComponent;
        $signature = Base64Url::encode($algorithm->sign($key, $dataToSign));

        return $dataToSign.'.'.$signature;
    }

    private function extractSignature($jwsString)
    {
        $p = strrpos($jwsString, '.');

        return [substr($jwsString, 0, $p), substr($jwsString, $p + 1) ?: ''];
    }

    /**
     * Only decodes jws string and returns headers and payload. To verify signature use verify method.
     *
     * @throws MalformedSignatureException
     * @param $jwsString
     * @return array(
     *                                     'headers' => array(),
     *                                     'payload' => payload data
     *                                     )
     */
    public function decode($jwsString)
    {
        $components = explode('.', $jwsString);
        if (count($components) !== 3) {
            throw new MalformedSignatureException('JWS string must contain 3 dot separated component.');
        }

        try {
            $headers = Json::decode(Base64Url::decode($components[0]));
            $payload = Json::decode(Base64Url::decode($components[1]));
        } catch (\InvalidArgumentException $e) {
            throw new MalformedSignatureException("Cannot decode signature headers and/or payload");
        }

        return [
            'headers' => $headers,
            'payload' => $payload
        ];
    }

    /**
     * @param $jwsString
     * @param $key
     *
     * @throws UnspecifiedAlgorithmException
     * @throws MalformedSignatureException
     * @throws UnspecifiedAlgorithmException
     * @throws InvalidSignatureException
     *
     * @return array(
     *                'headers' => array(),
     *                'payload' => mixed payload data
     *                )
     */
    public function verify($jwsString, $key)
    {
        $jws = $this->decode($jwsString);
        $headers = $jws['headers'];

        list($dataToSign, $signature) = $this->extractSignature($jwsString);

        if (empty($headers['alg'])) {
            throw new UnspecifiedAlgorithmException("No algorithm information found in headers. alg header parameter is required.");
        }

        $algorithm = $this->_getAlgorithm($headers['alg']);
        if (!$algorithm->verify($key, $dataToSign, Base64Url::decode($signature))) {
            throw new InvalidSignatureException("Invalid signature");
        }

        return $jws;
    }
}
