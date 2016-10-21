<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * ToDo: not working
 * Model for help in common routines.
 */
final class Urls
{
    /**
     * Adds params to URL.
     *
     * @param $url
     * @param $params array - [
     *      'PARAM_NAME' => 'PARAM_VALUE',
     * ]
     * @return string - result url
     */
    public static final function addParams($url, $params)
    {
        $query = parse_url($url, PHP_URL_QUERY);

        $resultParamsArr = [];
        foreach ($params as $name => $val) {
            $resultParamsArr[] = $name . '=' . $val;
        }
        $resultParamsStr = implode('&', $resultParamsArr);

        if ($query) {
            $url .= '&' . $resultParamsStr;
        } else {
            $url .= '?' . $resultParamsStr;
        }
        return $url;
    }
}