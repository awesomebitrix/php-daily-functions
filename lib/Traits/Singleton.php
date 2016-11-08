<?php

namespace bfday\PHPDailyFunctions\Traits;

trait Singleton
{
    private static $instance;

    /**
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $reflection     = new \ReflectionClass(__CLASS__);
            self::$instance = $reflection->newInstanceArgs(func_get_args());
        }

        return self::$instance;
    }
    final private function __clone(){}
    final private function __wakeup(){}
}