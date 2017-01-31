<?
namespace bfday\PHPDailyFunctions\Traits\Cache;

use bfday\PHPDailyFunctions\Helpers\Strings;

/**
 * How to use:
 * $this->cacheSetStorageProvider($cacheStorageProvider);
 * if (($cachedData = $this->cacheSetStorageProvider(new \CPHPCache())->setCacheTime(8640000)->cacheGetData(__METHOD__, null, $methodInputData)) === null) {
 *      // generate $cacheData
 *      // save it
 *      $this->cacheSaveData($cachedData);
 * };
 *
 * ToDo: test this Trait for multiple usage in single object
 * ToDo: cache interface required =(
 * ToDo: refactor as standalone class
 *
 * Class ObjectMethodResultCache
 * @package bfday\PHPDailyFunctions\Traits
 */
trait MethodResult
{
    /**
     * @var int - number of seconds to keep cache.
     */
    private $cacheTime = 3600;

    private $cacheStorageProvider;

    private $cacheBaseDir;

    private $cacheUpdateNeeded = false;

    /**
     * @param int $cacheTime
     *
     * @return $this
     * @throws \Exception
     */
    public function setCacheTime($cacheTime)
    {
        if (intval($cacheTime) <= 0) {
            throw new \Exception('$cacheTime cannot be less zero.');
        }
        $this->cacheTime = $cacheTime;

        return $this;
    }

    /**
     * @param $cacheStorageProvider
     *
     * @return $this
     * @throws \Exception
     */
    protected function cacheSetStorageProvider($cacheStorageProvider)
    {
        if (empty($cacheStorageProvider)) {
            throw new \Exception('$cacheStorageProvider cannot be empty.');
        }
        $this->cacheStorageProvider = $cacheStorageProvider;

        return $this;
    }

    /**
     * @param string $fullMethodName - use __METHOD__ magic var in calling method
     * @param        $cacheId
     * @param null   $inputParams    - you can generate $cacheId manually or this method can do it for you using
     *                               $inputParams
     *
     * @return mixed|null - NULL if no data for current cache or data from cache
     *
     * @throws \Exception
     */
    protected function cacheGetData($fullMethodName, $cacheId = null, $inputParams = null)
    {
        $this->cacheCheckData();

        if ($cacheId === null or $cacheId === false) {
            if ($inputParams === false) {
                throw new \Exception('$cacheId and $inputParams cannot be empty simultaneously.');
            } else {
                $cacheId = serialize($inputParams);
            }
        }

        if (empty($fullMethodName)) {
            throw new \Exception('$fullMethodName cannot be empty.');
        }

        $cacheDir = Strings::stringMultipleReplace(
            $fullMethodName . DIRECTORY_SEPARATOR,
            [
                '\\' => "_",
                '::' => "__",
            ]
        );

        if ($this->cacheStorageProvider->initCache(
            $this->cacheTime,
            $cacheId,
            $cacheDir)
        ) {
            $this->cacheUpdateNeeded = false;

            return $this->cacheStorageProvider->getVars();
        } else {
            $this->cacheUpdateNeeded = true;
        }

        return null;
    }

    private function cacheCheckData()
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
    protected function cacheSaveData($cacheData)
    {
        if ($this->cacheUpdateNeeded === false) {
            throw new \Exception('You tried to save data when its not needed.');
        }

        $this->cacheCheckData();

        if ($this->cacheStorageProvider->startDataCache()) {
            $this->cacheStorageProvider->endDataCache($cacheData);
        } else {
            throw new \Exception('Some error occur when tried to start cache data.');
        }

        return true;
    }
}