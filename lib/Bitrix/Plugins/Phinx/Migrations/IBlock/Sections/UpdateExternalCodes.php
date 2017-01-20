<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\IBlock\Sections;

use bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\Base;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Entity\Query;

class UpdateExternalCodes extends Base
{
    /**
     * Modes. Don't forget update ::initCommonData() method after adding new mode.
     */
    const MODE__EQUAL_TO_ID          = 1;
    const MODE__EQUAL_TO_CODE        = 2;
    const MODE__UNIQUE_VALUE_BY_ID   = 3;
    const MODE__UNIQUE_VALUE_BY_CODE = 4;

    // settings

    /**
     * @var string - fills up manually
     */
    protected $iBlockCode;

    /**
     * @var int - fills up automatically
     */
    protected $iBlockId;

    /**
     * @var array - fills up manually, depends on $mode
     */
    protected $sectionsData;

    /**
     * @var mixed - fills up manually, depends on available modes
     */
    protected $mode;

    /**
     * @var int - number of sections to process per 1 cicle (to not occupy large memory piece)
     */
    protected $processSectionsPerStep = 3;

    public function up()
    {
        parent::up();

        $this->initSettings();

        $sectionPropertyExtCode = "XML_ID";

        switch ($this->mode) {
            case static::MODE__EQUAL_TO_ID:
                $sections = [];
                $page = 1;
                do {
                    $sectionQuery = new Query(SectionTable::getEntity());
                    $sections = $sectionQuery
                        ->setOrder([
                            "ID" => "ASC",
                        ])
                        ->setSelect([
                            "ID",
                            "NAME",
                            $sectionPropertyExtCode,
                        ])
                        ->setFilter([
                            "IBLOCK_ID" => $this->iBlockId,
                        ])
                        ->setLimit($this->processSectionsPerStep)
                        ->setOffset(($page - 1) * $this->processSectionsPerStep)
                        ->exec()
                        ->fetchAll()
                    ;

                    foreach ($sections as $section) {
                        if ($section['ID'] != $section[$sectionPropertyExtCode]) {
                            // update XML_ID of section
                            $this->getOutput()
                                 ->writeln("Section ({$section['ID']} {$section[$sectionPropertyExtCode]} {$section['NAME']}) have been updated.")
                            ;
                            SectionTable::update($section['ID'],
                                [
                                    $sectionPropertyExtCode => $section['ID'],
                                ]
                            );
                        }
                    }

                    $page++;
                } while (count($sections) >= $this->processSectionsPerStep);
                break;
            case static::MODE__EQUAL_TO_CODE:
                // ToDo: implement logic
                throw new \Exception("Sorry, but selected [mode] have not been implemnted yet.");
                break;
            case static::MODE__UNIQUE_VALUE_BY_ID:
                // ToDo: implement logic
                throw new \Exception("Sorry, but selected [mode] have not been implemnted yet.");
                break;
            case static::MODE__UNIQUE_VALUE_BY_CODE:
                // ToDo: implement logic
                throw new \Exception("Sorry, but selected [mode] have not been implemnted yet.");
                break;
        }
    }

    /**
     * Inits initial state of object.
     *
     * @throws \Exception
     */
    private function initSettings()
    {
        if (empty($this->iBlockCode) || !is_string($this->iBlockCode)) {
            throw new \Exception("Property (iBlockCode) should have string type and cannot be empty.");
        }

        $this->iBlockId = \bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock::getInstance()
                                                                        ->getIBlockIDsByCodes($this->iBlockCode)[$this->iBlockCode];
        if ($this->iBlockId === false) {
            throw new \Exception("IBlock ({$this->iBlockCode}) not found.");
        }

        if (empty($this->mode) || !in_array($this->mode, [
                static::MODE__EQUAL_TO_ID,
                static::MODE__EQUAL_TO_CODE,
                static::MODE__UNIQUE_VALUE_BY_ID,
                static::MODE__UNIQUE_VALUE_BY_CODE,
            ])
        ) {
            throw new \Exception("Not supported value for property [mode]. Explicit.");
        }

        $this->processSectionsPerStep = intval($this->processSectionsPerStep);
        if ($this->processSectionsPerStep <= 0) {
            throw new \Exception("Not supported value for property [processSectionsPerStep]. It must have int value greater than 0.");
        }

        \Bitrix\Main\Loader::includeModule('iblock');
    }

    public function down()
    {
        parent::down();

        $this->initSettings();

        $this->getOutput()
             ->writeln("No reverse migration is needed.")
        ;
    }
}