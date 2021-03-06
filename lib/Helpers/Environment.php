<?php

namespace bfday\PHPDailyFunctions\Helpers;

use bfday\PHPDailyFunctions\Traits\Singleton;

/**
 * @method static|Environment getInstance()
 * Class EnvironmentHelper
 * @package bfday\PHPDailyFunctions\Helpers
 */
class Environment
{
    use Singleton;

    const CHECK_METHOD__HTTP_HOST = 0b1; // 1
    const CHECK_METHOD__SERVER_PARAMS = 0b10; // 2
    const CHECK_METHOD__ALL = 0b11; // 3

    /**
     * @var bool
     */
    private $isProd;

    /**
     * @var int
     */
    private $usedCheckMethod = 0;

    /**
     * @var array - array of server host. Should look like:
     * [
     *  POSSIBLE_NAME_1,
     *  ...
     * ]
     */
    protected $serverHosts;

    /**
     * @var array - array which will be used to test $_SERVER variable
     * [
     *      PARAM_NAME_1 => VAL_1,
     *      ...
     * ]
     * if VAL_X is array, so system assumes that VAL_X can have one that values, OR condition.
     * Used to consist of HOSTNAME and PWD params:
     * [
     *      'HOSTNAME' => 'my.host.name.com',
     *      'PWD'|'DOCUMENT_ROOT' => '/path/to/web/available/dir',
     * ]
     */
    protected $serverParamsToCheck;

    /**
     * EnvironmentHelper constructor.
     * @param $serverHosts
     * @param $serverParamsToCheck
     */
    public function __construct($serverHosts, $serverParamsToCheck)
    {
        $this->init($serverHosts, $serverParamsToCheck);
    }

    /**
     * Init object with selected params. This function can be used to reInit the object after __construct to reuse it.
     *
     * @param array $serverHosts
     * @param array $serverParamsToCheck
     * @return bool
     * @throws \Exception
     */
    public function init($serverHosts = [], $serverParamsToCheck = [])
    {
        if (!is_array($this->serverHosts) || !is_array($this->serverParamsToCheck) || empty($this->serverHosts) || empty($this->serverParamsToCheck)) {
            if (!is_array($serverHosts) || !is_array($serverParamsToCheck) || empty($serverHosts) || empty($serverParamsToCheck)) {
                throw new \Exception("All params must have array type and couldn't be empty.");
            }
            $this->serverHosts = array_values($serverHosts);
            $this->serverParamsToCheck = $serverParamsToCheck;
        }

        if ($this->checkServerParams() === false) {
            $this->isProd = false;
            return false;
        };

        if ($this->checkServerHost() === false) {
            $this->isProd = false;
            return false;
        };

        $this->isProd = true;
        return true;
    }

    protected function checkServerParams()
    {
        foreach ($this->serverParamsToCheck as $paramKey => $paramVal) {
            if (isset($_SERVER[$paramKey])) {
                $this->usedCheckMethod = $this->usedCheckMethod | static::CHECK_METHOD__SERVER_PARAMS;
                if (!is_array($paramVal)) {
                    if ($_SERVER[$paramKey] != $paramVal) {
                        return false;
                    }
                } elseif (!in_array($_SERVER[$paramKey], $paramVal)) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkServerHost()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->usedCheckMethod = $this->usedCheckMethod | static::CHECK_METHOD__HTTP_HOST;
            if (!in_array($_SERVER['HTTP_HOST'], $this->serverHosts)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isProd()
    {
        return $this->isProd;
    }

    /**
     * @return int
     */
    public function getUsedCheckMethod()
    {
        return $this->usedCheckMethod;
    }
}