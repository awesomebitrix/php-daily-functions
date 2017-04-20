<?
namespace bfday\PHPDailyFunctions\Traits;

trait ObjectExtender
{
    /**
     * Resets all object properties to null value. It has been created to reuse object instance
     */
    protected function resetObjectProperties()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
}