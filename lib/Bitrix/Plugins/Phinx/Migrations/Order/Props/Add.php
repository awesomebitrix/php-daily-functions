<?php

namespace bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\Order\Props;

use bfday\PHPDailyFunctions\Bitrix\Plugins\Phinx\Migrations\Base;
use bfday\PHPDailyFunctions\Helpers\Arrays;

class Add extends Base
{
    /**
     * @var - array with settings for every order prop (@link
     *      http://dev.1c-bitrix.ru/api_help/sale/classes/csaleorderprops/csaleorderprops__add.b64a5ac9.php) like this:
     *      [
     *          [
     *              "PERSON_TYPE_ID"  => \Willsmart\Bitrix\Modules\Shop\Order::PERSON_TYPE__NATURAL__ID,
     *              "NAME"            => "Офис",
     *              "TYPE"            => "TEXT",
     *              "REQUIED"         => "N",
     *              "DEFAULT_VALUE"   => "",
     *              "SORT"            => "500",
     *              "USER_PROPS"      => "Y",
     *              "IS_LOCATION"     => "N",
     *              "PROPS_GROUP_ID"  => \Willsmart\Bitrix\Modules\Shop\Order::PROPS_GROUP__CONTACT_INFO__ID,
     *              "SIZE1"           => 30,
     *              "SIZE2"           => 1,
     *              "DESCRIPTION"     => "Офис",
     *              "IS_EMAIL"        => "N",
     *              "IS_PROFILE_NAME" => "N",
     *              "IS_PAYER"        => "N",
     *              "IS_LOCATION4TAX" => "N",
     *              "CODE"            => $code,
     *              "IS_FILTERED"     => "Y",
     *              "IS_ZIP"          => "N",
     *              "UTIL"            => "N",
     *          ],
     *          ...
     *      ]
     */
    protected $props;

    /**
     * @var \CSaleOrderProps
     */
    private $saleOrderProps;

    public function up()
    {
        parent::up();

        $this->initSettings();

        $o = $this->getOutput();

        foreach ($this->props as $prop) {
            $id = $this->saleOrderProps->Add($prop);
            if ($id) {
                $o->writeln("Property ({$prop["CODE"]}, ID={$id}) have been added.");
            } else {
                $o->writeln("Some error occur when adding property ({$prop["CODE"]}).");
            }
        }
    }

    /**
     * Inits initial state of object.
     *
     * @throws \Exception
     */
    private function initSettings()
    {
        if (empty($this->props) || !is_array($this->props)) {
            throw new \Exception('$this->props should have appropriate array');
        }
        \Bitrix\Main\Loader::includeModule('sale');

        $this->saleOrderProps = new \CSaleOrderProps();
    }

    public function down()
    {
        parent::down();

        $this->initSettings();

        $o = $this->getOutput();

        foreach ($this->props as $prop) {
            $propFilter = Arrays::fetchOnlyKeys($prop, [
                "PERSON_TYPE_ID",
                "CODE",
                "PROPS_GROUP_ID",
            ]);
            $dbOrderProps = $this->saleOrderProps->GetList(
                [],
                $propFilter
            );
            if ($arOrderProps = $dbOrderProps->Fetch()) {
                $this->saleOrderProps->Delete($arOrderProps["ID"]);
                $o->writeln("Property ({$arOrderProps["CODE"]}, ID={$arOrderProps["ID"]}) have been deleted.");
            } else {
                $o->writeln("Property ({$arOrderProps["CODE"]}, ID={$arOrderProps["ID"]}) not found.");
            }
        }
    }
}