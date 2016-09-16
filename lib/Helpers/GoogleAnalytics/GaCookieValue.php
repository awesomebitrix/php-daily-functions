<?php

namespace bfday\PHPDailyFunctions\Helpers\GoogleAnalytics;

class GaCookieValue
{
    const COOKIE_NAME = '_ga';
    const FORMAT = '^GA(\d+)\.(\d+)\.(\d+)\.(\d+)$';

    protected $value;
    protected $version;
    protected $subdomainsNumber;
    protected $uid;
    protected $timestamp;

    public function __construct($value = null)
    {
        if (empty($value)) {
            $value = $_COOKIE[static::COOKIE_NAME];
        }

        $this->setValue($value);
    }

    public function setValue($value)
    {
        $matches = [];
        $check = preg_match('/' . static::FORMAT . '/i', $value, $matches);
        if ($check === 0)
            throw new \ErrorException('Wrong value format.');
        if ($check === false)
            throw new \ErrorException('Some error occur when checking GA value.');

        $this->version = $matches[1];
        $this->subdomainsNumber = $matches[2];
        $this->uid = $matches[3];
        $this->timestamp = $matches[4];
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getSubdomainsNumber()
    {
        return $this->subdomainsNumber;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function toArray()
    {
        return [
            'version' => $this->version,
            'subdomainsNumber' => $this->subdomainsNumber,
            'uid' => $this->uid,
            'timestamp' => $this->timestamp,
        ];
    }
}