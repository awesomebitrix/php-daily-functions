<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations;

class IBlockSetFields extends Base
{
    /**
     * @var array - array like
     * [
     *      'IBLOCK_CODE' => [
     *            IBLOCK_FIELD_CODE_1 => IBLOCK_FIELD_VALUE_1,
     *            ...
     *      ]
     * ]
     */
    protected $iBlockFields;

    public function up()
    {
        parent::up();

        \Bitrix\Main\Loader::includeModule('iblock');
        $obBlock = new \CIBlock();
        $iBlockCodes = array_keys($this->iBlockFields);
        $iBlockCodeIds = \bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock::getInstance()
                                                                       ->getIBlockIDsByCodes($iBlockCodes)
        ;
        foreach ($iBlockCodeIds as $iBlockCodeCode => $iBlockCodeId) {
            if ($iBlockCodeId !== false) {
                $obBlock->Update(
                    $iBlockCodeId,
                    $this->iBlockFields[ $iBlockCodeCode ]
                );
                $this->getOutput()
                     ->writeln("IBlock {{$iBlockCodeCode}, ID={$iBlockCodeId}} have been updated.")
                ;
            } else {
                $this->getOutput()
                     ->writeln("IBlock with CODE {{$iBlockCodeCode}} not found.")
                ;
            }
        }
    }

    public function down()
    {
        parent::down();

        $this->getOutput()
             ->writeln("No reverse migration is needed.")
        ;
    }
}