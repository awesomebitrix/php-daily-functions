<?php

namespace bfday\PHPDailyFunctions\Engine\Cache;

use bfday\PHPDailyFunctions\Helpers\Strings;
use bfday\PHPDailyFunctions\Helpers\System;

/**
 * How to use:
 * ```
 * $cache = new \bfday\PHPDailyFunctions\Engine\Cache\MethodResult();
 * $newData = ['newData'];
 * if (($dataFromCache = $cache
 *                              ->setStorageProvider(new \CPHPCache())
 *                              ->setTime(3000)
 *                              ->getData(null, $inputData)) === null
 * ) {
 *      $cache->saveData($newData);
 * }
 * ```
 * ! don't forget to provide canonical view for $newData
 *
 * ToDo: test this Class for multiple usage in single object
 * ToDo: cache interface required
 *
 * Class MethodResult
 * @package bfday\PHPDailyFunctions\Engine\Cache
 */
class MethodResult
{
    const DEFAULT_RELATIVE_CACHE_PATH = "/bitrix/cache";
    /**
     * @var int - number of seconds to keep cache.
     */
    private $cacheTime = 3600;

    private $cacheStorageProvider;

    private $cacheUpdateNeeded = false;

    private $cacheDir;

    /**
     * @param int $cacheTime
     *
     * @return $this
     * @throws \Exception
     */
    public function setTime($cacheTime)
    {
        if (intval($cacheTime) <= 0) {
            throw new \Exception('$cacheTime cannot be less zero.');
        }
        $this->cacheTime = $cacheTime;

        return $this;
    }

    /**
     * @param $fullMethodName - use __METHOD__ as a parameter for this function.
     *
     * @return $this
     * @throws \Exception
     */
    public function drop()
    {
        if (empty($this->cacheDir)) {
            throw new \Exception('$this->cacheDir is empty.');
        }

        $cacheDirPath = realpath($_SERVER["DOCUMENT_ROOT"]) . static::DEFAULT_RELATIVE_CACHE_PATH . DIRECTORY_SEPARATOR . $this->cacheDir;
        System::deleteDirectory($cacheDirPath);

        return $this;
    }

    /**
     * @param $cacheStorageProvider
     *
     * @return $this
     * @throws \Exception
     */
    public function setStorageProvider($cacheStorageProvider)
    {
        if (empty($cacheStorageProvider)) {
            throw new \Exception('$cacheStorageProvider cannot be empty.');
        }
        $this->cacheStorageProvider = $cacheStorageProvider;

        return $this;
    }

    /**
     * @param        $cacheId
     * @param null   $inputParams   - you can generate $cacheId manually or this method can do it for you using
     *                              $inputParams
     *
     * @return mixed|null - NULL if no data for current cache or data from cache
     *
     * @throws \Exception
     */
    public function getData($cacheId = null, $inputParams = null)
    {
        $bTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        if (!is_array($bTrace)) {
            throw new \Exception("[debug_backtrace] error result");
        } elseif (count($bTrace) < 1) {
            throw new \Exception("Not function call is not supported");
        }
        $bTrace = $bTrace[1];
        $cacheDir = $bTrace["file"] . (isset($bTrace["class"]) ? $bTrace["class"] : "") . "_" . $bTrace["function"];

        $this->checkData();

        if ($cacheId === null or $cacheId === false) {
            if ($inputParams === false) {
                throw new \Exception('$cacheId and $inputParams cannot be empty simultaneously.');
            } else {
                $cacheId = serialize($inputParams);
            }
        }

        $cacheDir = Strings::stringMultipleReplace(
            $cacheDir,
            [
                '\\' => "_",
                '::' => "__",
                '/' => "_",
            ]
        );
        $this->cacheDir = $cacheDir;

        if (
            $this->cacheStorageProvider->initCache(
                $this->cacheTime,
                $cacheId,
                $cacheDir
            )
        ) {
            $this->cacheUpdateNeeded = false;

            return $this->cacheStorageProvider->getVars();
        } else {
            $this->cacheUpdateNeeded = true;
        }

        return null;
    }

    private function checkData()
    {
        if (empty($this->cacheStorageProvider)) {
            throw new \Exception('Before using methods you have to initialize $cacheStorageProvider.');
        }
        if (intval($this->cacheTime) <= 0) {
            throw new \Exception('$cacheTime cannot be less zero.');
        }

        return true;
    }

    /**
     * @param $cacheData - data that have to be cached
     *
     * @return bool - FALSE on error
     * @throws \Exception
     */
    public function saveData($cacheData)
    {
        if ($this->cacheUpdateNeeded === false) {
            throw new \Exception('You tried to save data when its not needed.');
        }

        $this->checkData();

        if ($this->cacheStorageProvider->startDataCache()) {
            $this->cacheStorageProvider->endDataCache($cacheData);
        } else {
            throw new \Exception('Some error occur when tried to start cache data.');
        }

        return true;
    }
}