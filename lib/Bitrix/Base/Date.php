<?php

namespace bfday\PHPDailyFunctions\Bitrix\Base;

class Date
{
    protected static $staticDataInitiated = false;
    
    const DATE_FORMAT_COMMON_TILL_DAY = 'j F Y';
    const DATE_FORMAT_COMMON_TILL_SECOND = 'j F Y HH:MI:SS';
    const DATE_FORMAT_COMMON_DOTTED = 'DD.MM.YYYY HH:MI:SS';

    protected static $dateFormats;

    /**
     * Call me baby.
     *
     * @return bool
     */
    protected static function init()
    {
        if (static::$staticDataInitiated) {
            return true;
        } else {
            static::$staticDataInitiated = true;
        }
        static::$dateFormats = [
            static::DATE_FORMAT_COMMON_TILL_DAY,
            static::DATE_FORMAT_COMMON_DOTTED,
        ];
        return true;
    }
    
    public static function convert($date, $fromFormat, $toFormat)
    {
        static::init();
        // ToDo: check $date for integrity
        if (!in_array($fromFormat, static::$dateFormats) || !in_array($toFormat, static::$dateFormats)) {
            throw new \Exception('$fromFormat and $toFormat must be in static::$dateFormats');
        }
        return \CIBlockFormatProperties::DateFormat($toFormat, MakeTimeStamp($date, $fromFormat));
    }

    public static function fromSiteFormatTo($date, $format = false)
    {
        static::init();
        if ($format == false) $format = static::DATE_FORMAT_COMMON_TILL_DAY;
        return static::convert($date, \CSite::GetDateFormat(), $format);
    }
    
    public static function fromSiteFormatToCommonWithoutCurrentYear($date)
    {
        static::init();
        return str_replace(' ' . date('Y'), '', static::fromSiteFormatTo($date));
    }
}