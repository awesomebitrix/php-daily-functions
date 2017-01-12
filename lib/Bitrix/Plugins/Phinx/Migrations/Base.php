<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations;

use Phinx\Migration\AbstractMigration;

class Base extends AbstractMigration
{
    public function up()
    {
        parent::up();

        $this->initCommonData();
    }

    /**
     * Method executes before "up()" and "down()".
     */
    protected function initCommonData()
    {
    }

    public function down()
    {
        parent::down();

        $this->initCommonData();
    }
}