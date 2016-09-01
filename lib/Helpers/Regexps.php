<?php
namespace bfday\PHPDailyFunctions\Helpers;

/**
 * ToDo: not working
 * Model for help in common routines.
 */
final class Regexps
{
    const SPECIAL_CHARS = "#$%^&*()+=-[]';,./{}|:<>?~";

    /**
     * Does this string contains special char?
     *
     * @param $string - hashstack
     * @return bool
     */
    public static final function isContainsSpecialChars($string)
    {
        return (false !== strpbrk($string, static::SPECIAL_CHARS));
    }

    /**
     * Shield special chars in string if string contains them.
     *
     * @param $string - hashstack
     * @return string - result
     */
    public static final function getShielded($string)
    {
        $newString = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            if (static::isContainsSpecialChars($char)) $char = '\\' . $char;
            $newString .= $char;
        }
        return $newString;
    }
}