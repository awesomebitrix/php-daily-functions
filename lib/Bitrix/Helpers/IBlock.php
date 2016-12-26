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
     * @var array - contains IDs of corresponding IBlocks by its codes.
     * If no such IBlock with code X - corresponding value equals to [false]
     */
    protected $arIBlockCodesIDs;

    public function __construct()
    {
        $this->dropCache();
    }

    /**
     * Returns IBlocks IDs by their codes. Uses Bitrix D7
     *
     * ToDo: cache results in memory?
     * @param $codes array|string
     * @return array
     * @throws \Exception
     */
    public function getIBlockIDsByCodes($codes)
    {
        if (empty($codes)) {
            throw new \Exception('$codes cannot be empty.');
        }
        if (!is_array($codes)) {
            if (is_string($codes)) {
                $codes = [$codes];
            } else {
                throw new \Exception('$codes must have string or array type.');
            }
        }
        $arIBlockCodesIDs = [];

        foreach ($codes as $k => $code) {
            if (isset($this->arIBlockCodesIDs[$code])) {
                $arIBlockCodesIDs[$code] = $this->arIBlockCodesIDs[$code];
                unset($codes[$k]);
            }
        }

        if (!empty($codes)) {
            Loader::includeModule('iblock');
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
                ->fetchAll();
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
            foreach ($codes as $code) {
                $arIBlockCodesIDs[$code] = false;
            }
        }

        return $arIBlockCodesIDs;
    }

    public function dropCache()
    {
        $this->arIBlockCodesIDs = [];
    }
}