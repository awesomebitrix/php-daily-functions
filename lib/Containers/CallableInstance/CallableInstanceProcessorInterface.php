<?
namespace bfday\PHPDailyFunctions\Containers\CallableInstance;

interface CallableInstanceProcessorInterface
{
    /**
     * It must return value
     *
     * @param CallableInstanceContainer $callableInstance
     *
     * @return mixed
     */
    public function run(CallableInstanceContainer $callableInstance);
}