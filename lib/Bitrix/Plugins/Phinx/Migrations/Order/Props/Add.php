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
     *              PERSON_TYPE_ID - тип плательщика;
     *              ACTIVE - активность;
     * NAME - название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на
     * соответствующем языке); TYPE - тип свойства. Допустимые значения: CHECKBOX - флаг; TEXT - строка текста; SELECT
     * - выпадающий список значений; MULTISELECT - список со множественным выбором; TEXTAREA - многострочный текст;
     * LOCATION - местоположение; RADIO - переключатель. REQUIED - флаг (Y/N) обязательное ли поле; DEFAULT_VALUE -
     * значение по умолчанию; SORT - индекс сортировки; USER_PROPS - флаг (Y/N) входит ли это свойство в профиль
     * покупателя; IS_LOCATION - флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта
     * стоимости доставки (только для свойств типа LOCATION); PROPS_GROUP_ID - код группы свойств; SIZE1 - ширина поля
     * (размер по горизонтали); SIZE2 - высота поля (размер по вертикали); DESCRIPTION - описание свойства; IS_EMAIL -
     * флаг (Y/N) использовать ли значение свойства как E-Mail покупателя; IS_PROFILE_NAME - флаг (Y/N) использовать ли
     * значение свойства как название профиля покупателя; IS_PAYER - флаг (Y/N) использовать ли значение свойства как
     * имя плательщика; IS_LOCATION4TAX - флаг (Y/N) использовать ли значение свойства как местоположение покупателя
     * для расчёта налогов (только для свойств типа LOCATION); CODE - символьный код свойства. IS_FILTERED - свойство
     * доступно в фильтре по заказам. С версии 10.0. IS_ZIP - использовать как почтовый индекс. С версии 10.0. UTIL -
     * позволяет использовать свойство только в административной части. С версии 11.0.
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