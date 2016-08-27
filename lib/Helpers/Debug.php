<?php

namespace bfday\PHPDailyFunctions\Helpers;

ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/manual-errors.log');
/**
 * Model for simple code debug.
 */
class Debug
{
    private static $timestamp;
    /**
     * Shorten for '<pre>'.print_r($var, true).'</pre>'
     *
     * @param $var
     * @return string
     */
    public static function viewVar($var){
        return '<pre>'.print_r($var, true).'</pre>';
    }

    /**
     * Shorten for error_log(print_r($var,true))
     *
     * @param $var
     * @return string
     */
    public static function logVar($var){
        error_log(print_r($var,true));
        return true;
    }

    /**
     * Initializing or updates timestamp for logging time points.
     */
    public static function updateTimestamp(){
        static::$timestamp = microtime(true);
    }

    /**
     * @return mixed - current timestamp minus reserved.
     */
    public static function getTimestampDiff(){
        return (microtime(true) - static::$timestamp);
    }

    /**
     * @return mixed - returns current timestamp minus reserved then updates timestamp to current.
     */
    public static function getTimestampDiffUpdate(){
        $diff = microtime(true) - static::$timestamp;
        static::updateTimestamp();
        return $diff;
    }
}