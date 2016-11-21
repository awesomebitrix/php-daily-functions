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
            $ar =& $ar[$specialKeyItem];
        }
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

    public static function getAllValuesBySpecialKey(&$ar, $specialKey, $keySplitter = '.')
    {
        static::init();

        if (!is_array($ar)) throw new \ErrorException('$ar param must be an array type.');
        $values = [];
        foreach ($ar as $item) {
            $value = static::getValueBySpecialKey($item, $specialKey);
            if (empty($value)) return $values;
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
}