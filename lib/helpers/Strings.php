<?php
namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for help in common routines.
 */
class Strings
{
    /**
     * Replaces every token taken from keys of $tokenValueArray by corresponding value taken from $tokenValueArray.
     *
     * @param $string - subject string.
     * @param $tokenValueArray - array(
     *  'token1' => 'replace_value1',
     *  'token2' => 'replace_value2',
     *  ...
     * )
     * @return string - result string.
     * @throws \Exception
     */
    static function stringMultipleReplace($string, $tokenValueArray){
        if (!is_array($tokenValueArray)) throw new \Exception('Variable $tokenValueArray in "' . __METHOD__ . '" should be an array.');
        $keys = array_keys($tokenValueArray);
        return str_replace($keys, $tokenValueArray, $string);
    }

    /**
     * Replaces every token taken from keys of $tokenValueArray by corresponding value taken from $tokenValueArray.
     * Keys of $tokenValueArray is regular expression without slashes at the begining and at the end).
     *
     * @param $string
     * @param $tokenValueArray - array(
     *  'RegExpToken1' => 'replaceValue1',
     *  'RegExpToken2' => 'replaceValue2',
     *  ...
     * )
     * @return mixed
     * @throws \Exception
     */
    static function stringMultipleRegExpReplace($string, $tokenValueArray){
        if (!is_array($tokenValueArray)) throw new \Exception('Variable $tokenValueArray in "' . __METHOD__ . '" should be an array.');
        $keys = array_keys($tokenValueArray);
        foreach ($keys as &$key){
            $key = '/' . $key . '/';
        }
        return preg_replace($keys, $tokenValueArray, $string);
    }
}