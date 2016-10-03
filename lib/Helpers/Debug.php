<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for simple code debug.
 */
class Debug
{
    private static $timestamp;

    private static $logFilePath;

    public static function init()
    {
        static::$logFilePath = $_SERVER['DOCUMENT_ROOT'] . '/manual-errors.log';
        ini_set('error_log', static::$logFilePath);
    }

    /**
     * @return mixed
     */
    public static function getLogFilePath()
    {
        if (static::$logFilePath === null) throw new \ErrorException('Init log file path before first use.');
        return self::$logFilePath;
    }

    public static function resetLogFile()
    {
        file_put_contents(static::$logFilePath, '');
    }

    /**
     * Shorten for '<pre>'.print_r($var, true).'</pre>'
     *
     * @param $var
     * @return string
     */
    public static function viewVar($var)
    {
        return '<pre>' . print_r($var, true) . '</pre>';
    }

    /**
     * Shorten for error_log(print_r($var,true))
     *
     * @param $var
     * @return string
     */
    public static function logVar($var)
    {
        file_put_contents(static::$logFilePath, print_r($var, true) . "\r\n", FILE_APPEND);
        return true;
    }

    /**
     * Initializing or updates timestamp for logging time points.
     */
    public static function updateTimestamp()
    {
        static::$timestamp = microtime(true);
    }

    /**
     * @return mixed - current timestamp minus reserved.
     */
    public static function getTimestampDiff()
    {
        return (microtime(true) - static::$timestamp);
    }

    /**
     * @return mixed - returns current timestamp minus reserved then updates timestamp to current.
     */
    public static function getTimestampDiffUpdate()
    {
        $diff = microtime(true) - static::$timestamp;
        static::updateTimestamp();
        return $diff;
    }
}

Debug::init();