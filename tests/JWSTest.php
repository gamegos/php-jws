<?php

namespace Gamegos\JWS;

class JWSTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportedAlgorithms()
    {
        $jws = new JWS();
        $supported = $jws->supportedAlgorithms();
        $this->assertContains('none', $supported, 'none algorithm supported');

        $this->assertContains('HS256', $supported, 'HS256 algorithm supported');
        $this->assertContains('HS384', $supported, 'HS384 algorithm supported');
        $this->assertContains('HS512', $supported, 'HS512 algorithm supported');

        $this->assertContains('RS256', $supported, 'RS256 algorithm supported');
        $this->assertContains('RS384', $supported, 'RS384 algorithm supported');
        $this->assertContains('RS512', $supported, 'RS512 algorithm supported');
    }

    public function testEncodeDecodeHMAC()
    {
        $headers = array(
            'http://example.com/custom-header' => 'custom-value'
        );

        $payload = array(
            'a' => 'b',
            'x' => 'y'
        );

        $key = 'da0278b7cdbf1ee613e47c42f8928a1bdf9f0bb3';

        $jws = new JWS();
        $algs = array('HS256', 'HS384', 'HS512');
        foreach ($algs as $alg) {

            $headers['alg'] = $alg;

            $encoded = $jws->encode($headers, $payload, $key);
            $decoded = $jws->decode($encoded, $key);

            //printf("%s: %s\n", $alg, $encoded);
            $this->assertEquals($headers, $decoded['headers']);
            $this->assertEquals($payload, $decoded['payload']);
        }
    }

    public function testEncodeDecodeRSA()
    {
        $headers = array(
            'http://example.com/custom-header' => 'custom-value'
        );

        $payload = array(
            'a' => 'b',
            'x' => 'y'
        );

        $publicKey = file_get_contents(__DIR__ . '/test_publickey.pem');
        $privateKey = file_get_contents(__DIR__ . '/test_privatekey.pem');

        $jws = new JWS();
        $algs = array('RS256', 'RS384', 'RS512');
        foreach ($algs as $alg) {

            $headers['alg'] = $alg;

            $encoded = $jws->encode($headers, $payload, $privateKey);
            $decoded = $jws->decode($encoded, $publicKey);

            //printf("%s: %s\n", $alg, $encoded);
            $this->assertEquals($headers, $decoded['headers']);
            $this->assertEquals($payload, $decoded['payload']);
        }
    }
}
