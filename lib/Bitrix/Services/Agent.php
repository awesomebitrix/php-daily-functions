<?php

namespace bfday\PHPDailyFunctions\Bitrix\Services;

class Agent
{
    /**
     * Init here your object vars and state.
     */
    protected static function init()
    {
    }

    /**
     * Process here your data.
     */
    protected static function processor()
    {
    }

    /**
     * Call this to start agent tasks.
     *
     * @return string
     */
    final public static function run()
    {
        static::init();
        static::processor();
        return '\\' . get_called_class() . '::run();';
    }
}