<?php

namespace bfday\PHPDailyFunctions\Bitrix\Helpers;
use bfday\PHPDailyFunctions\Traits\Singleton;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Entity\Query;

/**
 * IBlock helper to help in common routines.
 *
 * @method IBlock getInstance()
 */
class IBlock
{
    use Singleton;

    /**
     * Returns IBlocks IDs by their codes. Uses Bitrix D7
     *
     * ToDo: cache result?
     * @param $codes
     * @return array
     * @throws \Exception
     */
    public function getIBlockIDsByCodes($codes)
    {
        if (empty($codes)) {
            throw new \Exception('$codes cannot be empty.');
        }
        if (!is_array($codes)) {
            $codes = [$codes];
        }
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
        $arIBlockCodeIDs = [];
        foreach ($arIBlocks as $arIBlock) {
            $arIBlockCodeIDs[$arIBlock['CODE']] = $arIBlock['ID'];
        }
        unset($arIBlocks);
        return $arIBlockCodeIDs;
    }
}