<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for simple code debug.
 */
class ExecTimeMeasurement
{
    /**
     * @var
     */
    private $timestamp;
    
    public function __construct()
    {
        $this->updateTimestamp();
    }

    /**
     * Initializing or updates timestamp for logging time points.
     */
    public function updateTimestamp()
    {
        $this->timestamp = microtime(true);
    }

    /**
     * @return mixed - current timestamp minus reserved.
     */
    public function getTimestampDiff()
    {
        return (microtime(true) - $this->timestamp);
    }

    /**
     * @return mixed - returns current timestamp minus reserved then updates timestamp to current.
     */
    public function getTimestampDiffUpdate()
    {
        $diff = microtime(true) - $this->timestamp;
        static::updateTimestamp();
        return $diff;
    }
}