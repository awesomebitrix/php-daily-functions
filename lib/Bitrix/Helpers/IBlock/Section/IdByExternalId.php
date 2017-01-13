<?php

namespace bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock\Section;

use bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock;
use bfday\PHPDailyFunctions\Traits\Singleton;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;

/**
 * IdByExternalId helper to help in common routines with sections IDs.
 * Caches data at disk os if you have problems with it - clear it's cache.
 *
 * @method static |IdByExternalId getInstance()
 */
class IdByExternalId
{
    use Singleton;

    /**
     * In memory cache array.
     *
     * @var array - contains IDs of corresponding IBlocks by its codes.
     * If no such IBlock with code X - corresponding value equals to [false]
     */
    protected $externalIdsItemIds;

    protected $diskCacheDir;

    /**
     * @var int - seconds
     */
    protected $cacheTime = 8640000;

    public function __construct()
    {
        $this->dropCache();
    }

    public function dropCache()
    {
        $this->externalIdsItemIds = [];
    }

    /**
     * Returns IBlocks IDs by their codes. Uses Bitrix D7
     *
     * ToDo: cache results at disk? check if found more than 1 element with repeatable code?
     *
     * @param $iBlockCode string
     * @param $codes      array|string
     *
     * @return array of string|bool
     * @throws \Exception
     */
    public function getIDsBy($iBlockCode, $codes)
    {
        if (empty($iBlockCode) || !is_string($iBlockCode)) {
            throw new \Exception('$iBlockCode cannot be empty.');
        }

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
            if (is_string($codes)) {
                $codes = [$codes];
            } else {
                throw new \Exception('$codes must have string or array type.');
            }
        }

        $externalIdsItemIds = [];

        // try to fetch from cache
        $cacheId = serialize($codes);
        $phpCache = new \CPHPCache();
        if ($phpCache->InitCache($this->cacheTime, $cacheId, "/" . str_replace("\\", "_", get_called_class()) . "/")) {
            $externalIdsItemIds = $phpCache->GetVars();
        } else {
            Loader::includeModule('iblock');
            $sectionQuery = new Query(SectionTable::getEntity());
            $sections = $sectionQuery
                ->setSelect([
                    "ID",
                    "XML_ID",
                ])
                ->setFilter([
                    "IBLOCK_ID" => IBlock::getInstance()
                                         ->getIBlockIDsByCodes($iBlockCode)[$iBlockCode],
                    "XML_ID"    => $codes,
                ])
                ->setLimit(2)
                ->exec()
                ->fetchAll()
            ;
            /*  Cannot be implemented =(
             * if (count($sections) > count()) {
                $phpCache->AbortDataCache();
                throw new \Exception("Found more than 1 element. This means that iblock contains not unique external codes. Fix it.");
            } else {
            */
            foreach ($codes as $code) {
                $externalIdsItemIds[$code] = false;
            }
            foreach ($sections as $section) {
                $externalIdsItemIds[$section["XML_ID"]] = $section["ID"];
            }
            //}
            if ($phpCache->StartDataCache()) {
                $phpCache->EndDataCache($externalIdsItemIds);
            }
        }

        return $externalIdsItemIds;
    }
}