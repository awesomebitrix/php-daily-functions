<?php

namespace bfday\PHPDailyFunctions\Bitrix\Helpers;

use bfday\PHPDailyFunctions\Traits\Cache\MethodResult;
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
    use MethodResult;

    /**
     * @var string - path to cache dir. relative to /bitrix/cache or absolute to system.
     */
    protected $cacheDir;

    /**
     * Returns IBlocks IDs by their codes. Uses Bitrix D7
     * Warning: all IBlock codes should be unique in all DB.
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

        Loader::includeModule('iblock');

        if (($arIBlockCodesIDs = $this->cacheSetStorageProvider(new \CPHPCache())
                                      ->setCacheTime(8640000)
                                      ->cacheGetData(__METHOD__, null, $codes)) === null
        ) {
            $arIBlockCodesIDs = [];

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
                    if (($k = array_search($arIBlock['CODE'], $codes)) !== false) {
                        unset($codes[$k]);
                    }
                }
                unset($arIBlocks);
            }

            if (!empty($codes)) {
                foreach ($codes as $code) {
                    $arIBlockCodesIDs[$code] = false;
                }
            }

            $this->cacheSaveData($arIBlockCodesIDs);
        }

        return $arIBlockCodesIDs;
    }
}