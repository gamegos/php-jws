<?php

namespace Gamegos\JWS;

class JWSTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportedAlgorithms()
    {
        $jws = new JWS();
        $supported = $jws->supportedAlgorithms();
        $this->assertContains('HS256', $supported, 'HS256 algorithm supported');
        $this->assertContains('HS384', $supported, 'HS384 algorithm supported');
        $this->assertContains('HS512', $supported, 'HS512 algorithm supported');

        $this->assertContains('RS256', $supported, 'RS256 algorithm supported');
        $this->assertContains('RS384', $supported, 'RS384 algorithm supported');
        $this->assertContains('RS512', $supported, 'RS512 algorithm supported');
    }

    public function testEncodeVerifyHMAC()
    {
        $headers = array(
            'http://example.com/custom-header' => 'custom-value',
        );

        $payload = array(
            'a' => 'b',
            'x' => 'y',
        );

        $key = 'da0278b7cdbf1ee613e47c42f8928a1bdf9f0bb3';

        $jws = new JWS();
        $algs = array('HS256', 'HS384', 'HS512');
        foreach ($algs as $alg) {
            $headers['alg'] = $alg;

            $encoded = $jws->encode($headers, $payload, $key);
            $decoded = $jws->verify($encoded, $key);

            $this->assertEquals($headers, $decoded['headers']);
            $this->assertEquals($payload, $decoded['payload']);
        }
    }

    public function testEncodeVerifyRSA()
    {
        $headers = array(
            'http://example.com/custom-header' => 'custom-value',
        );

        $payload = array(
            'a' => 'b',
            'x' => 'y',
        );

        $publicKey = file_get_contents(__DIR__.'/test_publickey.pem');
        $privateKey = file_get_contents(__DIR__.'/test_privatekey.pem');

        $jws = new JWS();
        $algs = array('RS256', 'RS384', 'RS512');
        foreach ($algs as $alg) {
            $headers['alg'] = $alg;

            $encoded = $jws->encode($headers, $payload, $privateKey);
            $decoded = $jws->verify($encoded, $publicKey);

            $this->assertEquals($headers, $decoded['headers']);
            $this->assertEquals($payload, $decoded['payload']);
        }
    }

    public function testExtractSignature()
    {
        $method = new \ReflectionMethod('Gamegos\JWS\JWS', 'extractSignature');
        $method->setAccessible(true);

        $this->assertEquals(['aaa.bbb', 'ccc'], $method->invoke(new JWS(), 'aaa.bbb.ccc'));
        $this->assertEquals(['nullsignature.payload', ''], $method->invoke(new JWS(), 'nullsignature.payload.'));
    }

    /**
     * @expectedException        \Gamegos\JWS\Exception\UnsupportedAlgorithmException
     */
    public function testUnsupportedAlgorithm()
    {
        $method = new \ReflectionMethod('Gamegos\JWS\JWS', '_getAlgorithm');
        $method->setAccessible(true);

        $method->invoke(new JWS(), 'FAKE_UNSUPPORTED_ALGORITHM');
    }

    /**
     * @expectedException        \Gamegos\JWS\Exception\UnspecifiedAlgorithmException
     */
    public function testUnspecifiedAlgorithm()
    {
        $jws = new JWS();
        $jws->encode([], 'payload', 'key');
    }

    /**
     * @expectedException        \Gamegos\JWS\Exception\MalformedSignatureException
     */
    public function testMalformedSignature()
    {
        $jws = new JWS();
        $jws->decode('two-dots.required');
    }
}
