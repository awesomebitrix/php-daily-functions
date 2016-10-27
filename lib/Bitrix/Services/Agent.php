<?php

namespace bfday\PHPDailyFunctions\Bitrix\Services;

class Agent
{
    /**
     * Init here your object vars and state.
     */
    protected static function init(){}

    /**
     * Process here your data.
     */
    protected static function processor(){}

    /**
     * @param $exception \Exception
     */
    protected static function exceptionHandler($exception){}

    /**
     * Call this to start agent tasks.
     *
     * @return string
     */
    final public static function run()
    {
        try {
            static::init();
            static::processor();
        } catch (\Exception $e) {
            static::exceptionHandler($e);
        }
        return '\\' . get_called_class() . '::run();';
    }
}