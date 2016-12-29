<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for simple code debug.
 */
class ExecTimeMeasurement
{
    /**
     * @var float - timestamp of routine start
     */
    private $timestamp;
    
    public function __construct()
    {
        $this->updateTimestamp();
    }

    /**
     * Initializing or updates timestamp for logging time points
     */
    public function updateTimestamp()
    {
        $this->timestamp = microtime(true);
    }

    /**
     * @return mixed - current timestamp minus reserved
     */
    public function getTimestampDiff()
    {
        return (microtime(true) - $this->timestamp);
    }

    /**
     * @return mixed - current timestamp minus reserved and updates start timestamp to current time
     */
    public function getTimestampDiffUpdate()
    {
        $diff = microtime(true) - $this->timestamp;
        static::updateTimestamp();
        return $diff;
    }
}