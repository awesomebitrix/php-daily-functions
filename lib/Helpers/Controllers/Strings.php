<?php
namespace bfday\PHPDailyFunctions\Helpers\Controllers;

/**
 * Model for help in common routines.
 */
class Strings
{
    /**
     * Converts string that represents controller name to controller class name.
     * Examples:
     *  'test' -> 'TestController',
     *  'test-test' -> 'TestTestController'
     *
     * @param $string - like 'catalog' or 'catalog-item'
     * @param string $classNamePostfix - like 'Controller' or 'Action' etc
     * @param string $splitter - URL nav item splitter like '-' or you may put you want
     * @return string - like 'Catalog' or 'CatalogItem', watch input param $string
     */
    public static final function navItemToClassName($string, $classNamePostfix = '', $splitter = '-')
    {
        $string = ucfirst($string);
        $string = preg_replace_callback("/$splitter([a-z])/", function($matches){
            return ucfirst($matches[1]);
        }, $string);
        return $string . $classNamePostfix;
    }

    /**
     * Converts class name string to string that represents controller.
     * Examples:
     *  'TestController' -> 'test'
     *  'TestTestController' -> 'test-test'
     *
     * @param $string - like 'Catalog' or 'CatalogItem'
     * @param string $classNamePostfix - like 'Controller' or 'Action' etc
     * @param string $splitter - URL nav item splitter like '-' or you may put you want
     * @return string - like 'catalog' or 'catalog-item', watch input param $string
     */
    public static final function classNameToNavItem($string, $classNamePostfix = '', $splitter = '-')
    {
        $string = lcfirst($string);
        // checks if $classNamePostfix is at the end of the string, then replaces it
        if (!empty($classNamePostfix) && strpos($string, $classNamePostfix) == strlen($string) - strlen($classNamePostfix))
            $string = substr_replace($string, '', strlen($string) - strlen($classNamePostfix), strlen($classNamePostfix));
        $string = preg_replace_callback("/([A-Z])/", function($matches) use($splitter){
            return $splitter . lcfirst($matches[1]);
        }, $string);
        return $string;
    }
}