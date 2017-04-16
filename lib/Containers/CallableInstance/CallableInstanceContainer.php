<?

namespace bfday\PHPDailyFunctions\Containers\CallableInstance;

/**
 * ToDO: in progress
 * Envelopes callable, static class or object so you can intercept calls
 * To call any method of decorated instance just use it's method names and corresponding params
 * So if you want hints in your code simply write above variable phpDoc like: var RequiredClassName
 *
 * @method mixed call(...$args) - calls simple callable if it was passed to decorator
 *
 * Class CallableInstanceContainer
 * @package bfday\PHPDailyFunctions\Containers\CallableInstance
 */
class CallableInstanceContainer
{
    const INSTANCE_TYPE__CALLABLE = 1;
    const INSTANCE_TYPE__CLASS    = 2;
    const INSTANCE_TYPE__OBJECT   = 3;

    const METHOD_NAME_FOR_CALLABLE_CALLS = 'call';

    /**
     * @var int
     */
    protected $callableInstanceType;
    /**
     * @var string|object
     */
    protected $callableInstance;
    /**
     * @var string
     */
    protected $methodName;
    /**
     * @var callable
     */
    protected $callable;
    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var CallableInstanceProcessorInterface
     */
    protected $processor;

    /**
     * @param  callable|string|object            $callableInstance - callable, path to class, object
     * @param CallableInstanceProcessorInterface $processor        - strategy which processes calls interceptions
     *
     * @throws \Exception
     */
    final function __construct($callableInstance, CallableInstanceProcessorInterface $processor)
    {
        switch (true) {
            case is_callable($callableInstance):
                $this->instanceType = static::INSTANCE_TYPE__CALLABLE;
                break;
            case class_exists($callableInstance):
                $this->instanceType = static::INSTANCE_TYPE__CALLABLE;
                break;
            case is_object($callableInstance):
                $this->instanceType = static::INSTANCE_TYPE__CALLABLE;
                break;
            default:
                throw new \Exception('Supports callable or objects');
        }
        $this->callableInstance = $callableInstance;
        $this->processor = $processor;
    }

    final function __call($name, $arguments)
    {
        switch ($this->callableInstanceType) {
            case static::INSTANCE_TYPE__CALLABLE:
                $methodName = static::METHOD_NAME_FOR_CALLABLE_CALLS;
                if ($name !== $methodName) {
                    throw new \Exception("To call callable through this decorator use '->$methodName' with params give through comma");
                }
                $this->callable = $this->callableInstance;
                break;
            case static::INSTANCE_TYPE__CLASS:
                if (!method_exists($this->callableInstance, $name)) {
                    throw new \Exception('Call of not existing static method');
                }
                $this->callable = $this->callableInstance . "::{$name}";
                break;
            case static::INSTANCE_TYPE__OBJECT:
                if (!method_exists($this->callableInstance, $name)) {
                    throw new \Exception('Call of not existing object method');
                }
                $this->callable = [$this->callableInstance, $name];
                break;
        }
        $this->methodName = $name;
        $this->arguments = $arguments;
        return $this->processor->run($this);
    }

    /**
     * @return mixed
     */
    public function getCallableInstanceType()
    {
        return $this->callableInstanceType;
    }

    /**
     * @return string|object
     */
    public function getCallableInstance()
    {
        return $this->callableInstance;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->arguments;
    }
}