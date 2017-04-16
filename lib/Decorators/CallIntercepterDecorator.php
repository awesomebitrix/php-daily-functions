<?

namespace bfday\PHPDailyFunctions\Decorators;

/**
 * ToDO: in progress
 *
 * Class CallThisDecorator
 * @package bfday\PHPDailyFunctions\Decorators
 */
class CallIntercepterDecorator
{
    const INSTANCE_TYPE__CALLABLE = 1;
    const INSTANCE_TYPE__CLASS    = 2;
    const INSTANCE_TYPE__OBJECT   = 3;

    const METHOD_NAME_FOR_CALLABLE_CALLS = 'callableCaller';

    /**
     * @var int
     */
    protected $decoratableInstanceType;
    /**
     * @var string|object
     */
    protected $decoratableInstance;
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
     * @var CallIntercepterProcessStrategyInterface
     */
    protected $processStrategy;

    final function __construct(CallIntercepterProcessStrategyInterface $processStrategy, $decoratableInstance)
    {
        if (is_callable($decoratableInstance)) {
            $this->instanceType = static::INSTANCE_TYPE__CALLABLE;
            echo 'INSTANCE_TYPE__CALLABLE' . "<br />";
        } elseif (class_exists($decoratableInstance)) {
            $this->instanceType = static::INSTANCE_TYPE__CLASS;
            echo 'INSTANCE_TYPE__CLASS' . "<br />";
        } elseif (is_object($decoratableInstance)) {
            $this->instanceType = static::INSTANCE_TYPE__OBJECT;
            echo 'INSTANCE_TYPE__OBJECT' . "<br />";
        } else {
            throw new \Exception('Supports callable or objects');
        }
        $this->decoratableInstance = $decoratableInstance;
    }

    final function __call($name, $arguments)
    {
        switch ($this->decoratableInstanceType) {
            case static::INSTANCE_TYPE__CALLABLE:
                $methodName = static::METHOD_NAME_FOR_CALLABLE_CALLS;
                if ($name !== $methodName) {
                    throw new \Exception("To call callable through this decorator use '->$methodName' with params give through comma");
                }
                $this->callable = $this->decoratableInstance;
                $this->arguments = $arguments;
                $this->processStrategy->process($this);
                break;
            case static::INSTANCE_TYPE__CLASS:
                if (!method_exists($this->decoratableInstance, $name)) {
                    throw new \Exception('Call of not existing static method');
                }
                $this->callable = $this->decoratableInstance . "::{$name}";
                $this->arguments = $arguments;
                $this->processStrategy->process($this);
                break;
            case static::INSTANCE_TYPE__OBJECT:
                if (!method_exists($this->decoratableInstance, $name)) {
                    throw new \Exception('Call of not existing object method');
                }
                $this->arguments = $arguments;
                $this->processStrategy->process($this);
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getDecoratableInstanceType()
    {
        return $this->decoratableInstanceType;
    }

    /**
     * @return string|object
     */
    public function getDecoratableInstance()
    {
        return $this->decoratableInstance;
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
    public function getArguments()
    {
        return $this->arguments;
    }
}