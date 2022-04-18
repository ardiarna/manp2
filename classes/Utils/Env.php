<?php

namespace Utils;

use Dotenv\Dotenv;

class Env
{
    /**
     * @var Dotenv
     */
    private static $_instance;

    private static $requiredParams = array(
        // for database
        'DB_HOST', 'DB_NAME', 'DB_USER',
        'MAIL_HOST', 'MAIL_USER', 'MAIL_PASS',
        'PALLET_AGING_REPORT_RECIPIENTS_TO',
        'REPORT_WEEKLY_PALLET_DEAD_STOCK_AND_SLOW_RECIPIENTS_TO'
    );

    private static function init() {
        if (!isset(self::$_instance)) {
            $dotenv = new Dotenv(dirname(dirname(__DIR__)));
            $dotenv->load();
            $dotenv->required(self::$requiredParams);

            self::$_instance = $dotenv;
        }
    }

    /**
     * Get value from environment variable.
     * @param string $var variable to get
     * @param mixed $defaultValue default value to send, if variable does not exist.
     * @return mixed
     */
    public static function get($var, $defaultValue = null) {
        self::init();

        return $_ENV[$var] ?: $defaultValue;
    }

    /**
     * Checks if an environment variable is set.
     * @param string|string[] $vars environment variable(s) to check.
     * @return bool
     */
    public static function has($vars) {
        self::init();

        if (is_array($vars)) {
            foreach ($vars as $var) {
                if (isset($_ENV[$var])) {
                    return true;
                }
            }
            return false;
        }
        return isset($_ENV[$vars]);
    }

    public static function isDebug() {
        return \RequestParamProcessor::getBoolean(self::get('DEBUG', false));
    }
}
