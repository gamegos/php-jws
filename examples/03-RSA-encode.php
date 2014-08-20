<?php
require_once __DIR__ . '/../vendor/autoload.php';

$headers = array(
    'alg' => 'RS256', //alg is required
    'typ' => 'JWT'
);

// anything that json serializable
$payload = array(
    'sub' => 'someone@example.com',
    'iat' => '1402993531'
);

$key = file_get_contents(__DIR__ . '/rsa_privatekey.pem');

$jws = new \Gamegos\JWS\JWS();
$jwsString = $jws->encode($headers, $payload, $key);
printf("%s\n", $jwsString); //eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJzb21lb25lQGV4YW1wbGUuY29tIiwiaWF0IjoiMTQwMjk5MzUzMSJ9.AXce7-bXUYkhKzAd5aRGpiTH3IqDIw5V1nORSUKgNAz8zvyFZ5Leq8P6IfpZ9vFj3tKeIyME0TZAUM9Lmpt1lwSEZj7u4_pRQox5Jt79gjA4bjX0_ZurR7lPOVXd8srcb8QQeW2RL7Ul5VMfbcqr6_lc8tilG_qZB6r9UhLNrRs
