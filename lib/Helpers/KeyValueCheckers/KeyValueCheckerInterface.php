<?
namespace bfday\PHPDailyFunctions\Helpers\KeyValueCheckers;

interface KeyValueCheckerInterface
{
    /**
     * @param $key int|string
     * @param $value mixed
     *
     * @return boolean
     */
    public function run($key, $value);
}