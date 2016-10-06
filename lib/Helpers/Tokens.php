<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Tokes helper
 */
class Tokens
{
    public static function generate($length = 32, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    /**
     * Generates always unique hex value.
     *
     * @param int $length
     * @return string
     */
    public static function generateUniqueHex($length = 64)
    {
        $length = floor($length / 2);
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public static function generateUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0C2f ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
        );
    }
}