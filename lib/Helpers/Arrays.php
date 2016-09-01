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
    const SORT_ORDERS = [
        self::SORT_ORDER_DESC,
        self::SORT_ORDER_ASC,
    ];

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
        if (!in_array($sortOrder, static::SORT_ORDERS))
            throw new \ErrorException('Wrong sort order type. Use SORT_ORDERS constant to define it.');
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
        if (strlen($keySplitter) == 0) throw new \ErrorException('Key splitter cannot be empty.');
        $specialKeyArray = explode($keySplitter, $specialKey);
        foreach ($specialKeyArray as $specialKeyItem) {
            if (strlen($specialKeyItem) == 0) throw new \ErrorException('Key item cannot be empty.');
            $ar =& $ar[$specialKeyItem];
            if (!isset($ar)) return null;
        }
        return $ar;
    }

    public static function setValueBySpecialKey(&$ar, $specialKey, $value, $keySplitter = '.')
    {
        if (strlen($keySplitter) == 0) throw new \ErrorException('Key splitter cannot be empty.');
        $specialKeyArray = explode($keySplitter, $specialKey);
        foreach ($specialKeyArray as $specialKeyItem) {
            if (strlen($specialKeyItem) == 0) throw new \ErrorException('Key item cannot be empty.');
            $ar =& $ar[$specialKeyItem];
            if (!isset($ar)) $ar = [];
        }
        $ar = $value;
    }
}