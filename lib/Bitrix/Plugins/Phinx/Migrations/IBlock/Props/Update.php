<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\IBlock\Props;

use bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\Base;

/**
 * How to use:
 * - Inside "initCommonData" fill $this->iBlockCode and $this->iBlockProperties with appropriate data
 * - Run migration
 *
 * Class Update
 * @package bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\IBlock\Props
 */
class Update extends Base
{
    const VALUE_SIMILAR_TO_PROPERTY_CODE = '#SIMILAR_TO_CODE#';
    const VALUE_SIMILAR_TO_PROPERTY_ID   = '#SIMILAR_TO_ID#';

    protected $iBlockCode;

    protected $iBlockId;

    /**
     * @var array - like
     *            [
     *              PROPERTY_CODE_1 => [
     *                 FIELD_1 => VALUE_1,
     *                 ...
     *              ],
     *              ...
     *            ]
     */
    protected $iBlockProperties;

    public function up()
    {
        parent::up();

        // update XML_IDs of properties
        $obBlockProperty = new \CIBlockProperty();
        foreach ($this->iBlockProperties as $iBlockPropertyCode => $iBlockPropertyFields) {
            $arIBlockPropertyData = $this->getPropertyByCode($iBlockPropertyCode);

            foreach ($iBlockPropertyFields as &$iBlockPropertyField) {
                if ($iBlockPropertyField === static::VALUE_SIMILAR_TO_PROPERTY_CODE) {
                    $iBlockPropertyField = $iBlockPropertyCode;
                } elseif ($iBlockPropertyField === static::VALUE_SIMILAR_TO_PROPERTY_ID) {
                    $iBlockPropertyField = $arIBlockPropertyData['ID'];
                }
                unset($iBlockPropertyField);
            }

            $iBlockPropertyFields["IBLOCK_ID"] = $this->iBlockId;

            if ($arIBlockPropertyData === false) {
                $this->getOutput()
                     ->writeln("Property ($iBlockPropertyCode) not found.")
                ;
            } else {
                $obBlockProperty->Update($arIBlockPropertyData['ID'], $iBlockPropertyFields);
                $this->getOutput()
                     ->writeln("Property ($iBlockPropertyCode, IBlockCode={$this->iBlockCode}, iBlockId={$this->iBlockId}) have been updated.")
                ;
            }
        }
    }

    protected function getPropertyByCode($code)
    {
        if (empty($code) || !is_string($code)) {
            throw new \Exception('$code must have string type and cannot be empty.');
        }
        $dbIBlockProperties = \CIBlockProperty::GetList(
            [],
            [
                "IBLOCK_CODE" => $this->iBlockCode,
                "CODE"        => $code,
            ]
        );

        return $dbIBlockProperties->GetNext();
    }

    public function down()
    {
        parent::down();

        $this->getOutput()
             ->writeln("No reverse migration is needed.")
        ;
    }

    /**
     * Don't forget in child method run "parent::initCommonData();" after init of object properties.
     *
     * @throws \Exception
     */
    protected function initCommonData()
    {
        if (empty($this->iBlockCode) || !is_string($this->iBlockCode)) {
            throw new \Exception("Property (iBlockCode) should have string type and cannot be empty.");
        }

        $this->iBlockId = \bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock::getInstance()
                                                                        ->getIBlockIDsByCodes($this->iBlockCode)[ $this->iBlockCode ];
        if ($this->iBlockId === false) {
            throw new \Exception("IBlock ({$this->iBlockCode}) not found.");
        }

        \Bitrix\Main\Loader::includeModule('iblock');
    }
}