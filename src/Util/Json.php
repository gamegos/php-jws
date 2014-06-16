<?php
namespace Gamegos\JWS\Util;

class Json
{
    public static function encode($data)
    {
        $json = @json_encode($data);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(self::transformJsonError());
        }

        return $json;
    }

    public static function decode($json)
    {
        $data = @json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(self::transformJsonError());
        }

        return $data;
    }

    /**
     * borrowed from Symfony\Component\HttpFoundation\JsonResponse class
     * @return string
     */
    protected static function transformJsonError()
    {
        if (function_exists('json_last_error_msg')) {
            return json_last_error_msg();
        }

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded.';

            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch.';

            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found.';

            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON.';

            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded.';

            default:
                return 'Unknown error.';
        }
    }
}
