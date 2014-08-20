<?php
require_once __DIR__ . '/../vendor/autoload.php';

$key = 'some-secret-for-hmac';

//jws encoded string
$jwsString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJzb21lb25lQGV4YW1wbGUuY29tIiwiaWF0IjoiMTQwMjk5MzUzMSJ9.0lgcQRnj_Jour8MLdIc71hPjjLVcQAOtagKVD9soaqU';

$jws = new \Gamegos\JWS\JWS();
print_r($jws->decode($jwsString, $key));
