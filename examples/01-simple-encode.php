<?php
require_once __DIR__ . '/../vendor/autoload.php';

$headers = array(
    'alg' => 'HS256', //alg is required
    'typ' => 'JWT'
);

// anything that json serializable
$payload = array(
    'sub' => 'someone@example.com',
    'iat' => '1402993531'
);

$key = 'some-secret-for-hmac';

$jws = new \Gamegos\JWS\JWS();
$jwsString = $jws->encode($headers, $payload, $key);
printf("%s\n", $jwsString); //eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJzb21lb25lQGV4YW1wbGUuY29tIiwiaWF0IjoiMTQwMjk5MzUzMSJ9.0lgcQRnj_Jour8MLdIc71hPjjLVcQAOtagKVD9soaqU%
