<?php

namespace bfday\PHPDailyFunctions\Helpers;

/**
 * Model for help in system routines.
 */
class System
{
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
    static public function requireBasicHttpAuth($credentials = array())
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

    static public function getMicrotimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}