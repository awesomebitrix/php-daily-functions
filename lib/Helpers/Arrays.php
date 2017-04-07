<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * ToDO: not working
 * Model for help in common routines.
 */
class Arrays
{
    const SORT_ORDER_DESC = 1;
    const SORT_ORDER_ASC = 2;
    static $sortOrders;

    const DEFAULT_KEY_SPLITTER = '.';

    public static function init()
    {
        static::$sortOrders = [
            self::SORT_ORDER_DESC,
            self::SORT_ORDER_ASC,
        ];
    }

    /**
     * [
     *  'first' => [ // $internalKeyLevel = 0
     *      'sort' => 3, // $internalKeyLevel = 1 ... etc
     *  ],
     *  'second' => [
     *      'sort' => 1,
     *  ],
     *  'third' => [
     *      'sort' => 2,
     *  ],
     * ];
     *
     * @param $ar array
     * @param $specialKey - key 'some.internal.key' that wil be expanded to $ar['FIRST_LEVEL_KEY']['some']['internal']['key']. You can change keys splitter by setting $keySplitter param
     * @param int $sortOrder
     * @param string $keySplitter
     * @return array
     * @throws \ErrorException
     */
    public static function sortByInternalKeys(&$ar, $specialKey, $sortOrder = self::SORT_ORDER_ASC, $keySplitter = '.')
    {
        static::init();

        if (!in_array($sortOrder, static::$sortOrders))
            throw new \ErrorException('Wrong sort order type. Use static::$sortOrders to define them.');
        $helpAr = [];
        foreach ($ar as $item) {
            $helpAr[] = static::getValueBySpecialKey($item, $specialKey, $keySplitter);
        }
        $sortOrder = ($sortOrder == static::SORT_ORDER_DESC ? SORT_DESC : SORT_ASC);
        array_multisort($helpAr, $sortOrder, $ar);
        return $ar;
    }

    public static function setValueBySpecialKey(&$ar, $specialKey, $value, $keySplitter = '.')
    {
        static::init();

        if (strlen($keySplitter) == 0) throw new \ErrorException('Key splitter cannot be empty.');
        if (!is_array($ar)) throw new \ErrorException('$ar param must be an array type.');
        $specialKeyArray = explode($keySplitter, $specialKey);
        foreach ($specialKeyArray as $specialKeyItem) {
            if (strlen($specialKeyItem) == 0) throw new \ErrorException('Key item cannot be empty.');
            if (!isset($ar[$specialKeyItem])) $ar[$specialKeyItem] = [];
            if (!is_array($ar[$specialKeyItem])) throw new \ErrorException('Value should be of array type.');
            $ar =& $ar[$specialKeyItem];
        }
        $ar = $value;
    }

    /**
     * @param $ar array -
     * @param $specialKey string - string like 'first.second.third' converts to ['first']['second']['third'].
     * @param string $keySplitter - key splitter. Dot by default. You can specify any.
     * @return null|mixed - value by special key.
     * @throws \ErrorException
     */
    public static function getValueBySpecialKey(&$ar, $specialKey, $keySplitter = '.')
    {
        static::init();

        if (strlen($keySplitter) == 0) throw new \ErrorException('Key splitter cannot be empty.');
        if (!is_array($ar)) throw new \ErrorException('$ar param must be an array type.');
        $specialKeyArray = explode($keySplitter, $specialKey);
        foreach ($specialKeyArray as $specialKeyItem) {
            if (strlen($specialKeyItem) == 0) throw new \ErrorException('Key item cannot be empty.');
            if (!isset($ar[$specialKeyItem])) return null;
            $ar =&$ar[$specialKeyItem];
        }
        return $ar;
    }

    /**
     * Gets all array values by SpecialKey
     *
     * @param        $ar
     * @param        $specialKey
     * @param string $keySplitter
     *
     * @return array - empty if no values found
     * @throws \ErrorException
     */
    public static function getAllValuesBySpecialKey(&$ar, $specialKey, $keySplitter = '.')
    {
        static::init();

        if (!is_array($ar)) throw new \ErrorException('$ar param must be an array type.');
        $values = [];
        foreach ($ar as $item) {
            $value = static::getValueBySpecialKey($item, $specialKey);
            if (!isset($value)) return $values;
            $values[] = static::getValueBySpecialKey($item, $specialKey);
        }
        return $values;
    }

    /**
     * Pushes $needle into $acceptor only if $needle unique against $acceptor. Returns true if push is OK. False - elsewhere.
     *
     * @param array $acceptor
     * @param $needle
     * @param null $key
     * @return bool
     * @throws \ErrorException
     */
    public static function pushIfUnique(&$acceptor, $needle, &$key = null)
    {
        if (!is_array($acceptor)) throw new \ErrorException('$acceptor param must be an array type.');
        if (!in_array($needle, $acceptor)) {
            if ($key !== null && empty($key)) {
                $acceptor[$needle] = $needle;
            } elseif ($key !== null) {
                $acceptor[$key] = $needle;
            } else {
                $acceptor[] = $needle;
            }
            return true;
        }
        return false;
    }

    /**
     * Creates new array from $source by leaving only $onlyKeys keys
     *
     * @param array $source
     * @param array $onlyKeys
     * @return array
     * @throws \Exception
     */
    public static function fetchOnlyKeys(&$source, $onlyKeys = []) {
        if (!is_array($source) || !is_array($onlyKeys)) {
            throw new \Exception('params have to be an array type');
        }
        return array_intersect_key($source, array_flip($onlyKeys));
    }

    /**
     * Creates new array from $source by excluding $excludedKeys keys
     *
     * @param array$source
     * @param array $excludedKeys
     * @return array
     * @throws \Exception
     */
    public static function fetchWithoutKeys(&$source, $excludedKeys = []) {
        if (!is_array($source) || !is_array($excludedKeys)) {
            throw new \Exception('params have to be an array type');
        }
        return array_intersect_key($source, array_diff_key($source, array_flip($excludedKeys)));
    }

    /**
     * Checks array $ar that he has all keys from $keys
     *
     * @param array $ar - array to examine
     * @param array $keys - array keys to examine
     * @param bool $isIgnoreCase
     * @return bool|array - returns required keys
     * @throws \Exception
     */
    public static function consistOfKeys($ar, $keys, $isIgnoreCase = false) {
        if (!is_array($ar) || !is_array($keys)) {
            throw new \Exception('Params must have array type.');
        }
        $diff = array_diff(array_keys($ar), $keys);
        if (count($diff)) {
            return $diff;
        } else {
            return true;
        }
    }

    /**
     * Checks array $ar that he contains all keys from $keys
     *
     * @param array $ar - array to examine
     * @param array $keys - array keys to examine
     * @param bool $isIgnoreCase
     * @return bool|array - returns required keys
     * @throws \Exception
     */
    public static function requireKeys($ar, $keysCodes, $isIgnoreCase = false) {
        if (!is_array($ar) || !is_array($keys)) {
            throw new \Exception('Params must have array type.');
        }
        $diff = array_diff($keys, array_keys($ar));
        if (count($diff)) {
            return $diff;
        } else {
            return true;
        }
    }

    /**
     * ToDo: NOT IMPLEMENTED YET
     * reorganizes array $ar according $keyStrategy (how to change every key) and $valueStrategy (how to change every value)
     *
     * @param $ar array
     * @param $keyStrategy - when is callable should have format "function($key, $val)"
     * @param $valueStrategy - when is callable should have format "function($key, $val)"
     *
     * @return mixed
     */
    public static function reorganize($ar, $keyStrategy = null, $valueStrategy = null)
    {
        if (!is_array($ar)) {
            throw new \Exception('Param $ar must have array type.');
        }

        if ($keyStrategy === null && $valueStrategy === null) {
            return $ar;
        }

        $res = [];

        if ($keyStrategy !== null) {
            if ($valueStrategy !== null) { // both are not null
                if (is_callable($keyStrategy)) {
                    if (is_callable($valueStrategy)) { // both are callable

                    } else { // $valueStrategy not callcable

                    }
                } else { // $keyStrategy not callable
                    if (is_callable($valueStrategy)) { // $valueStrategy is callable

                    } else { // both not callable
                        //
                    }
                }
            } else { // $valueStrategy === null
            }
        } elseif ($valueStrategy !== null) {

        } else {
            throw new \Exception("Some unpredictable error occur in " . __METHOD__);
        }

        return $res;
    }
}