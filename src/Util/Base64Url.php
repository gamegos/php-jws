<?php
namespace Gamegos\JWS\Util;

class Base64Url
{
    /**
     * from: http://www.php.net/manual/en/function.base64-encode.php#103849
     * @param $data
     * @return string
     */
    public static function encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * from: http://www.php.net/manual/en/function.base64-encode.php#103849
     * @param $data
     * @return string
     */
    public static function decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}
