<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for help in common routines.
 */
class Common
{
    /**
     * Truncates string using $desiredLength as number chars to truncate and uses words to make upper bound.
     *
     * @param $string - input string
     * @param $desiredLength - desired width
     * @param string $suffix
     * @return string - result string
     */
    static function truncateStringByCharsWords($string, $desiredLength, $suffix = '')
    {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $desiredLength) {
                if (!empty($suffix) && strpos($parts[$last_part-1], $suffix)===false) {
                    if ($parts[$last_part-1] = ' ') $parts[$last_part-1] = '';
                    $parts[$last_part++] = $suffix;
                }
                break;
            }
        }

        return implode(array_slice($parts, 0, $last_part));
    }

    /**
     * Plural form founder. Takes $number and $pluralForms of words and outputs resulting plural form.
     *
     * @param $number - number to pluralize. Ex.: 100
     * @param $pluralForms - tree plural forms to substitute after $number. Ex.: array('день','дня','дней'), array('арбуз', 'арбуза', 'арбузов')
     * @return string - plural form.
     * @throws \Exception
     */
    static function getPluralForm($number, $pluralForms)
    {
        if (!is_int($number)) throw new \Exception('Variable $number in  "' . __METHOD__ . '" should be an integer.');
        if (!is_array($pluralForms)) throw new \Exception('Variable $forms in "' . __METHOD__ . '" should be an array.');
        if (!(count($pluralForms) == 3)) throw new \Exception('Variable $forms in "' . __METHOD__ . '" should contain three elements.');

        return $number % 10 == 1 && $number % 100 != 11 ? $pluralForms[0] : ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20) ? $pluralForms[1] : $pluralForms[2]);
    }

    /**
     * Pluralizer. Takes $number and $pluralForms of words outputs $number.$pluralForms[i] after $number analysis.
     *
     * @param $number - number to pluralize. Ex.: 100
     * @param $pluralForms - tree plural forms to substitute after $number. Ex.: array('день','дня','дней')
     * @param string $splitter - splitter between number and plural form. Ex.: ' '
     * @return string - $number . $splitter . [pluralForm].
     * @throws \Exception
     */
    static function pluralizeNumber($number, $pluralForms, $splitter = ' ')
    {
        $result = $number . $splitter . self::getPluralForm($number, $pluralForms);
        return $result;
    }

    /**
     * Formats price string.
     *
     * @param $price
     * @param int $digitsAfterDecimal
     * @param string $decimalDivider
     * @param string $thousandsSeparator
     * @return string
     * @throws \Exception
     */
    static function formatPrice($price, $digitsAfterDecimal = 0, $decimalDivider = '.', $thousandsSeparator = ' ')
    {
        if (!(is_float($price) || is_int($price) || is_numeric($price))) throw new \Exception('Variable $price in  "' . __METHOD__ . '" should be of float, int or numeric type.');
        if (!is_int($digitsAfterDecimal) || $digitsAfterDecimal < 0) throw new \Exception('Variable $digitsAfterDecimal in  "' . __METHOD__ . '" should be an integer and greater than 0.');
        return number_format($price, $digitsAfterDecimal, $decimalDivider, $thousandsSeparator);
    }

    /**
     * @param float $price
     * @param int $digitsAfterDecimal
     * @param string $decimalDivider
     * @return string
     * @throws \Exception
     */
    static function formatFloat($price, $digitsAfterDecimal = 2, $decimalDivider = '.')
    {
        $thousandsSeparator = '';
        if (!is_float($price)) throw new \Exception('Variable $price in  "' . __METHOD__ . '" should have float type.');
        if (!is_int($digitsAfterDecimal) || $digitsAfterDecimal < 0) throw new \Exception('Variable $digitsAfterDecimal in  "' . __METHOD__ . '" should be an integer and greater than 0.');
        return number_format($price, $digitsAfterDecimal, $decimalDivider, $thousandsSeparator);
    }

    /**
     * Formats $price and adds to it corresponding plural form.
     *
     * @param $price
     * @param int $digitsAfterDecimal
     * @param string $decimalDivider
     * @param string $thousandsSeparator
     * @param $pluralForms
     * @param string $pluralSplitter
     * @return string
     * @throws \Exception
     */
    static function formatPriceAddPluralForm($price, $digitsAfterDecimal = 0, $decimalDivider = '.', $thousandsSeparator = ' ', $pluralForms, $pluralSplitter = ' ')
    {
        return static::formatPrice($price, $digitsAfterDecimal, $decimalDivider, $thousandsSeparator) . $pluralSplitter . static::getPluralForm($price, $pluralForms);
    }

    /**
     * Returns copyright years divided by $splitter in manner like "xxxx - yyyy". If years are equals then only one year will be outputted.
     *
     * @param $yearBegin - number that represents begin year.
     * @param string $yearEnd - number that represents end year. contains number or 'today' string.
     * @param string $splitter - splitter that uses to split two years in string.
     * @return string - result string.
     * @throws \Exception
     */
    static function copyrightYears($yearBegin, $yearEnd = 'today', $splitter = ' - ')
    {
        if ($yearEnd == 'today') $yearEnd = date('Y');
        if (!is_int($yearBegin)) throw new \Exception('Variable $number in class "' . __METHOD__ . '" should be an integer.');
        if (!is_int($yearEnd)) throw new \Exception('Variable $number in class "' . __METHOD__ . '" should be an integer.');
        return implode($splitter, array_unique(Array($yearBegin, date('Y'))));
    }

    /**
     * Redirects to $url using redirect $code.
     *
     * @param $url - url like http://domain.xxx/...
     * @param int $code - redirect code.
     */
    static function redirectTo($url, $code = 301)
    {
        if (isset($url)) header('Location: ' . $url, true, $code);
    }

    static function getURLProtocolPrefix()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol;
    }
}