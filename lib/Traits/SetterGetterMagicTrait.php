<?php

namespace bfday\PHPDailyFunctions\Traits;

trait SetterGetterMagicTrait
{
    public function __get($var)
    {
        $getter = 'get' . ucfirst($var);
        $var = '_' . $var;

        if (method_exists($this, $getter)) {
            try {
                $val = $this->$getter();
            } catch (\Exception $e) {
                throw new \Exception($e);
            }
            return $val;
        } elseif (property_exists($this, $var)) {
            return $this->$var;
        } else {
            throw new \Exception('Can not get property: ' . $var . ', method ' . $getter . ' and property itself not exists');
        }
    }

    public function __set($var, $val)
    {
        $setter = 'set' . ucfirst($var);
        $var = '_' . $var;

        if (method_exists($this, $setter)) {
            try {
                $setval = $this->$setter($val);
            } catch (\Exception $e) {
                throw new \Exception($e);
            }
            $this->$var = ($setval === NULL) ? $this->$var : $setval;
        } elseif (property_exists($this, $var)) {
            $this->$var = $val;
        } else {
            throw new \Exception('Can not set property: ' . $var . ', method ' . $setter . ' not exists');
        }
    }
}