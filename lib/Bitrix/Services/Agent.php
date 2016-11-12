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
     * If this function returns ===true then agent executes. If other - just quits.
     * @return bool
     */
    protected static function isOn() {
        return true;
    }

    /**
     * Call this to start agent tasks.
     *
     * @return string
     */
    final public static function run()
    {
        if (static::isOn() === true) {
            try {
                static::init();
                static::processor();
            } catch (\Exception $e) {
                static::exceptionHandler($e);
            }
        }
        return '\\' . get_called_class() . '::run();';
    }
}