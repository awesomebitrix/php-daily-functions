<?php

namespace bfday\PHPDailyFunctions\Bitrix\Helpers;

use bfday\PHPDailyFunctions\Traits\Singleton;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;

/**
 * IBlock helper to help in common routines.
 *
 * @method static |IBlock getInstance()
 */
class IBlock
{
    use Singleton;

    /**
     * In memory cache array.
     *
     * @var array - contains IDs of corresponding IBlocks by its codes.
     * If no such IBlock with code X - corresponding value equals to [false]
     */
    protected $arIBlockCodesIDs;

    public function __construct()
    {
        $this->dropCache();
    }

    public function dropCache()
    {
        $this->arIBlockCodesIDs = [];
    }

    protected $diskCacheDir;

    /**
     * @var int - seconds
     */
    protected $cacheTime = 8640000;

    /**
     * Returns IBlocks IDs by their codes. Uses Bitrix D7
     *
     * ToDo: cache results at disk?
     *
     * @param $codes array|string
     *
     * @return array of string|bool
     * @throws \Exception
     */
    public function getIBlockIDsByCodes($codes)
    {
        if (empty($codes)) {
            throw new \Exception('$codes cannot be empty.');
        } elseif (is_array($codes)) {
            // remove duplicate codes or throw exception?
            if (count($codes) != count(array_unique($codes))) {
                throw new \Exception('$codes cannot contain duplicate values.');
            }
            // maybe resort is needed to exclude similar cache ids
            sort($codes);
        } elseif (!is_array($codes)) {
            if (is_string($codes) || is_int($codes)) {
                $codes = [$codes];
            } else {
                throw new \Exception('$codes must have string, integer or array type.');
            }
        }

        $arIBlockCodesIDs = [];

        Loader::includeModule('iblock');

        // try to fetch from cache
        $cacheId = serialize($codes);
        $phpCache = new \CPHPCache();
        if ($phpCache->InitCache($this->cacheTime, $cacheId, "/" . str_replace("\\", "_", get_called_class()) . "/")) {
            $arIBlockCodesIDs = $phpCache->GetVars();
        } else {
            foreach ($codes as $k => $code) {
                if (isset($this->arIBlockCodesIDs[$code])) {
                    $arIBlockCodesIDs[$code] = $this->arIBlockCodesIDs[$code];
                    unset($codes[$k]);
                }
            }

            if (!empty($codes)) {
                $query = new Query(IblockTable::getEntity());
                $arIBlocks = $query
                    ->setFilter([
                        'CODE' => $codes,
                    ])
                    ->setSelect([
                        'ID',
                        'CODE',
                    ])
                    ->exec()
                    ->fetchAll()
                ;
                foreach ($arIBlocks as $arIBlock) {
                    $arIBlockCodesIDs[$arIBlock['CODE']] = $arIBlock['ID'];
                    $this->arIBlockCodesIDs[$arIBlock['CODE']] = $arIBlock['ID'];
                    if (($k = array_search($arIBlock['CODE'], $codes)) !== false) {
                        unset($codes[$k]);
                    }
                }
                unset($arIBlocks);
            }

            if (!empty($codes)) {
                //$codes = implode(";", $codes);
                //throw new \Exception("IBlocks with codes [$codes] was not found. Maybe they were deleted. Remove references from code.");
                foreach ($codes as $code) {
                    $arIBlockCodesIDs[$code] = false;
                }
            }

            if ($phpCache->StartDataCache()) {
                $phpCache->EndDataCache($arIBlockCodesIDs);
            }
        }

        return $arIBlockCodesIDs;
    }
}