<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations;

use Phinx\Migration\AbstractMigration;

class IBlockUpdatePropsExternalCodes extends AbstractMigration
{
    const VALUE_SIMILAR_TO_PROPERTY_CODE = '#SIMILAR_TO_CODE#';
    const VALUE_SIMILAR_TO_PROPERTY_ID   = '#SIMILAR_TO_ID#';

    protected $iBlockCode;

    protected $iBlockId;

    protected $iBlockProperties;


    public function up()
    {
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

            if ($arIBlockPropertyData === false) {
                $this->getOutput()
                     ->writeln("Property ($iBlockPropertyCode) not found.")
                ;
            } else {
                $obBlockProperty->Update($arIBlockPropertyData['ID'], $iBlockPropertyFields);
                $this->getOutput()
                     ->writeln("Property ($iBlockPropertyCode) have been updated.")
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
        $this->getOutput()
             ->writeln("No reverse migration is needed.")
        ;
    }

    /**
     * Dont forget in child method run "parent::init();" before write your code.
     *
     * @throws \Exception
     */
    protected function init()
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