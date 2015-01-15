JSON Web Signature (JWS) PHP Library
====================================

A simple and extensible PHP implementation of JWS based on JWS draft](http://tools.ietf.org/html/draft-ietf-jose-json-web-signature).

## Note

**[gamegos/jwt](https://github.com/Gamegos/php-jwt) library is more suitable for a JSON WEB TOKEN(JWT) solution** 


## Installation


The recommended way to install gamegos/jws is through [Composer](http://getcomposer.org).

```JSON
{
    "require": {
        "gamegos/jws": "~1.0"
    }
}
```


## Basic Usage

Encoding

```php
$headers = array(
    'alg' => 'HS256', //alg is required. see *Algorithms* section for supported algorithms
    'typ' => 'JWT'
);

// anything that json serializable
$payload = array(
    'sub' => 'someone@example.com',
    'iat' => '1402993531'
);

$key = 'some-secret-for-hmac';

$jws = new \Gamegos\JWS\JWS();
echo $jws->encode($headers, $payload, $key);
//eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJzb21lb25lQGV4YW1wbGUuY29tIiwiaWF0IjoiMTQwMjk5MzUzMSJ9.0lgcQRnj_Jour8MLdIc71hPjjLVcQAOtagKVD9soaqU

```


Decoding & Verifying

```php

$key = 'some-secret-for-hmac';

//jws encoded string
$jwsString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJzb21lb25lQGV4YW1wbGUuY29tIiwiaWF0IjoiMTQwMjk5MzUzMSJ9.0lgcQRnj_Jour8MLdIc71hPjjLVcQAOtagKVD9soaqU';

$jws = new \Gamegos\JWS\JWS();

$jws->verify($jwsString, $key);
```

If everything is ok you will get an array with 'headers' and 'payload'.

```php
/*
Array
(
    [headers] => Array
        (
            [alg] => HS256
            [typ] => JWT
        )

    [payload] => Array
        (
            [sub] => someone@example.com
            [iat] => 1402993531
        )

)
*/
```

You will get one of [these exceptions](#exceptions) if something bad happens. 

If you only want to parse jws string **without** signature verification you can use ```decode``` method.

```php

$jws->decode($jwsString);
```



## Supported Algorithms

Currently these algorithms are supported.

| alg Parameter    | Digital Signature or MAC Algorithm    |
|------------------|---------------------------------------|
| HS256            | HMAC using SHA-256                    |
| HS384            | HMAC using SHA-384                    |
| HS512            | HMAC using SHA-512                    |
| RS256<sup>1</sup>| RSASSA-PKCS-v1_5 using SHA-256        |
| RS384<sup>1</sup>| RSASSA-PKCS-v1_5 using SHA-384        |
| RS512<sup>1</sup>| RSASSA-PKCS-v1_5 using SHA-512        |
| none             | No digital signature or MAC performed |


See [JWA Cryptographic Algorithms for Digital Signatures and MACs](http://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-27#section-3) page for full list of defined algorithms for JWS.


## Exceptions

- ```InvalidSignatureException```
- ```MalformedSignatureException```
- ```UnspecifiedAlgorithmException```
- ```UnsupportedAlgorithmException```



## Extending: Adding New Signature/MAC Algorithm

Create an **algorithm class** that implements \Gamegos\JWS\Algorithm\AlgorithmInterface.

```php
//example NoneAlgorithm
class NoneAlgorithm implements \Gamegos\JWS\Algorithm\AlgorithmInterface
{

    public function sign($key, $data)
    {
        return '';
    }

    public function verify($key, $data, $signature)
    {
        return (string) $signature === '';
    }
}

```


Register your class:

```php
//...
$jws = new \Gamegos\JWS\JWS();
$jws->registerAlgorithm('my-new-algorithm', new NoneAlgorithm());
```

Now you can use ```my-new-algorithm``` as a usual 'alg' parameter.


## Known Limitations

- *JWS JSON Serialization* is not supported.

---------------------

<sup>1</sup> requires [php openssl module](http://www.php.net/manual/en/book.openssl.php).
