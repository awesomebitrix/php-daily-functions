<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for help in system routines.
 */
class System
{
    static protected $isProdEnvironment;
    static protected $prodDomains;
    static protected $additionalProdParams;
    /**
     * Basic auth like php function
     *
     * @param array $credentials - array of arrays:
     *  [
     *  'password' =>' your_pass',
     *  'login' => 'your_loogin'
     *  ]
     * @throws \Exception
     */
    public static function requireBasicHttpAuth($credentials = array())
    {
        if (!is_array($credentials)) throw new \Exception('Parameter $credentials in "' . __METHOD__ . '" should be an array.');
        $credentialsFound = false;
        if (count($credentials)) {
            foreach ($credentials as $credential) {
                if (!empty($credential['password']) && !empty($credential['login']) && $credential['login'] == $_SERVER['PHP_AUTH_USER'] && $credential['password'] == $_SERVER['PHP_AUTH_PW']){
                    $credentialsFound = true;
                    break;
                }
            }

            if (!$credentialsFound) {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Auth required.';
                exit;
            }
        }
    }

    public static function getMicrotimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * @param array $prodDomains
     * @throws \ErrorException
     */
    public static function initProdDomains($prodDomains)
    {
        if (($prodDomains !== null) && (is_array($prodDomains))) {
            static::$prodDomains = $prodDomains;
        } else {
            throw new \ErrorException('Property $prodDomains must be of array type.');
        }
    }

    /**
     * Checks if this environment is production environment.
     * @return bool
     * @internal param array|null $prodDomains
     */
    public static function isProdEnvironment()
    {
        return in_array($_SERVER['HTTP_HOST'], static::getProdDomains());
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public static function getProdDomains()
    {
        if (static::$prodDomains === null) {
            throw new \ErrorException('Property $prodDomains is not initialized.');
        }
        return self::$prodDomains;
    }

    /**
     * Deletes directory recursively.
     *
     * @param $path
     * @return bool
     */
    public static function deleteDirectory($path) {
        if (!file_exists($path)) {
            return true;
        }

        if (!is_dir($path)) {
            return unlink($path);
        }

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!self::deleteDirectory($path . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($path);
    }
}