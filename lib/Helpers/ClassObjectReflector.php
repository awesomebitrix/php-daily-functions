<?php

namespace bfday\PHPDailyFunctions\Helpers;
use bfday\PHPDailyFunctions\Helpers\KeyValueCheckers\KeyValueCheckerInterface;
use bfday\PHPDailyFunctions\Traits\ObjectExtender;

/**
 * Common routines for classes
 *
 * Model for help in common routines.
 */
class ClassObjectReflector
{
    use ObjectExtender;

    protected $reflection;

    /**
     * @param $classNameOrObject string|object
     */
    public function __construct($classNameOrObject) {
        $this->reflection = new \ReflectionClass($classNameOrObject);
    }

    /**
     * @param $keyValueChecker KeyValueCheckerInterface - key/value strategy, must have signature like "function ($nameOfConstant,
     *                        $valueOfConstant)" and return true (if your want constant in result) or false (otherwise)
     * ToDo: refactor, create predefined $keyValueChecker strategies
     *
     * @return array
     * @throws \Exception
     */
    public function getConstants(KeyValueCheckerInterface $keyValueChecker = null) {
        if ($keyValueChecker === null) {
            return $this->getReflection()->getConstants();
        }

        $constantsNamesValues = [];
        foreach ($this->getReflection()->getConstants() as $name => $value) {
            $ckeckResult = $keyValueChecker->run($name, $value);
            if ($ckeckResult === true) {
                $constantsNamesValues[$name] = $value;
            } elseif ($ckeckResult !== false) {
                throw new \Exception('$keyValueChecker returned not true/false value - redesign it to return true/false');
            };
        };
        return $constantsNamesValues;
    }

    /**
     * @return \ReflectionClass
     * @throws \Exception
     */
    public function getReflection()
    {
        if (!isset($this->reflection)) {
            throw new \Exception('$this->reflection property must be set');
        }
        return $this->reflection;
    }
}