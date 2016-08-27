<?php

namespace Core\Traits;

trait SetGetExtendedTrait
{
    public function __get($var){
        $getter = 'get' . ucfirst($var);
        $var = '_' . $var;

        if(method_exists($this, $getter)){
            try{
                $val = $this->$getter();
            }catch(\Exception $e){
                throw new \Exception($e);
            }
            return $val;
        }
        throw new \Exception('Can not get property: ' . $var . ', method ' . $getter . ' not exists');
    }

    public function __set($var, $val){
        $setter = 'set' . ucfirst($var);
        $var = '_' . $var;

        if(method_exists($this, $setter) && isset($this->$var)){
            try{
                $setval = $this->$setter($val);
            }catch(\Exception $e){
                throw new \Exception($e);
            }
            $this->$var = ($setval === NULL) ? $this->$var : $setval;
        }else{
            throw new \Exception('Can not set property: ' . $var . ', method ' . $setter . ' not exists');
        }
    }
}