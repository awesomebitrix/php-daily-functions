<?php

namespace bfday\PHPDailyFunctions\Bitrix\Helpers;

/**
 * Model for help in common routines.
 */
class Bitrix
{
    /**
     * Gets IBlockElement by it's $id and returns it's property value by $propertyCode param
     * @param $id
     * @param string $propertyCode
     * @return null
     */
    static public function getIBlockElementById($id, $propertyCode = 'PREVIEW_TEXT')
    {
        if (!\CModule::IncludeModule("iblock")) return null;
        $res = \CIBlockElement::GetByID($id);
        $ar_res = $res->GetNext();
        if ($ar_res && !empty($ar_res[$propertyCode])) {
            return $ar_res[$propertyCode];
        } else return null;
    }

    /**
     * ToDo: refactor needed.
     * @param $id
     * @param string $propertyCode
     * @return string
     */
    static public function getEditableIBlockElementById($IblockID, $id, $propertyCode = 'PREVIEW_TEXT')
    {
        global $APPLICATION;
        ob_start();
        $APPLICATION->IncludeComponent(
            "sberlife:editable_element",
            "",
            Array(
                "DISPLAY_DATE" => "Y",
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "Y",
                "SHARE_TEMPLATE" => "",
                "SHARE_HANDLERS" => array(""),
                "SHARE_SHORTEN_URL_LOGIN" => "",
                "SHARE_SHORTEN_URL_KEY" => "",
                "AJAX_MODE" => "N",
                "IBLOCK_TYPE" => "page_elements",
                "IBLOCK_ID" => $IblockID,
                "ELEMENT_ID" => $id,
                "ELEMENT_CODE" => "",
                "FIELD_CODE" => Array("ID"),
                "PROPERTY_CODE" => Array("DESCRIPTION"),
                "IBLOCK_URL" => "news.php?ID=#IBLOCK_ID#\"",
                "DETAIL_URL" => "",
                "SET_TITLE" => "Y",
                "SET_CANONICAL_URL" => "Y",
                "SET_BROWSER_TITLE" => "Y",
                "BROWSER_TITLE" => "-",
                "SET_META_KEYWORDS" => "Y",
                "META_KEYWORDS" => "-",
                "SET_META_DESCRIPTION" => "Y",
                "META_DESCRIPTION" => "-",
                "SET_STATUS_404" => "Y",
                "SET_LAST_MODIFIED" => "Y",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                "ADD_SECTIONS_CHAIN" => "Y",
                "ADD_ELEMENT_CHAIN" => "N",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "USE_PERMISSIONS" => "N",
                "GROUP_PERMISSIONS" => Array("1"),
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "CACHE_GROUPS" => "Y",
                "DISPLAY_TOP_PAGER" => "Y",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "Страница",
                "PAGER_TEMPLATE" => "",
                "PAGER_SHOW_ALL" => "Y",
                "PAGER_BASE_LINK_ENABLE" => "Y",
                "SHOW_404" => "Y",
                "MESSAGE_404" => "",
                "PAGER_BASE_LINK" => "",
                "PAGER_PARAMS_NAME" => "arrPager",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "HIDE_ICONS" => "Y",
            )
        );
        return ob_get_clean();
    }
}