<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations;

use Phinx\Migration\AbstractMigration;

class Base extends AbstractMigration
{
    /**
     * Don't forget call "parent::up();" in derived class
     */
    public function up()
    {
        parent::up();

        $this->initCommonData();
    }

    /**
     * Method executes before "up()" and "down()".
     */
    protected function initCommonData() {}

    /**
     * Don't forget call "parent::down();" in derived class
     */
    public function down()
    {
        parent::down();

        $this->initCommonData();
    }
}