<?php

namespace bfday\PHPDailyFunctions\Traits;

trait Singleton
{
    private static $instance;

    /**
     * Runs once on Singleton instance creation. So u can separate logic of instance and object init.
     */
    protected function instanceInit(){}

    /**
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $reflection     = new \ReflectionClass(get_called_class());
            self::$instance = $reflection->newInstanceArgs(func_get_args());

            self::$instance->instanceInit();
        }

        return self::$instance;
    }
    final private function __clone(){}
    final private function __wakeup(){}
}