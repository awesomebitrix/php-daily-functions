<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model to help in Price manipulating.
 */
class Price
{
    /**
     * @param $oldPrice
     * @param $newPrice
     *
     * @return int|float
     * @throws \Exception
     */
    static public function calcDiscountCoefficient($oldPrice, $newPrice)
    {
        if (!(is_float($oldPrice) || is_int($oldPrice) || is_numeric($oldPrice))) {
            throw new \Exception('Variable $oldPrice in  "' . __METHOD__ . '" should be of float, int or numeric type.');
        }
        if (!(is_float($newPrice) || is_int($newPrice) || is_numeric($newPrice))) {
            throw new \Exception('Variable $newPrice in  "' . __METHOD__ . '" should be of float, int or numeric type.');
        }
        return (1 - ($newPrice / $oldPrice));
    }

    /**
     * @param $oldPrice
     * @param $newPrice
     *
     * @return int
     */
    static public function calcDiscountPercent($oldPrice, $newPrice)
    {
        return intval(round(static::calcDiscountCoefficient($oldPrice, $newPrice) * 100));
    }
}