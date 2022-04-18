<?php

namespace Utils;

use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RequestParamProcessor;

class FileLog
{
    /**
     * Path to log directory
     * @var string
     */
    private static $LOG_DIR;
    /**
     * DEBUG
     * @var bool
     */
    private static $DEBUG;

    private static $_instances = array();

    /**
     * Get instance of Logger
     * If debug is set to true, will give DEBUG level logger.
     * Otherwise, log level will be set to NOTICE.
     *
     * @param string $channel channel to log the file to. Will be used as filename.
     * @return Logger
     * @throws \Exception
     */
    public static function getInstance($channel = 'app') {
        $baseDir = dirname(dirname(__DIR__));

        if (!isset(static::$LOG_DIR) || !isset(static::$DEBUG)) {
            $dotenv = new Dotenv($baseDir);
            $dotenv->load();
            $dotenv->required(array('DEBUG', 'LOG_DIR'))->notEmpty();

            static::$LOG_DIR = $baseDir . DIRECTORY_SEPARATOR . $_ENV['LOG_DIR'];
            static::$DEBUG = RequestParamProcessor::getBoolean($_ENV['DEBUG']);
        }

        if (!isset(static::$_instances[$channel])) {
            // setup mail
            $logger = new Logger("file-$channel");

            $fileName = static::$LOG_DIR . DIRECTORY_SEPARATOR . "${channel}.log";
            $formatter = new LineFormatter(null, null, false, true);
            $fileHandler = new StreamHandler($fileName, static::$DEBUG ? Logger::DEBUG : Logger::INFO);
            $fileHandler->setFormatter($formatter);

            $logger->pushHandler($fileHandler);
            if (static::$DEBUG) {
                $stdoutHandler = new StreamHandler('php://stdout');
                $stdoutHandler->setFormatter($formatter);
                $logger->pushHandler($stdoutHandler);
            }
            static::$_instances[$channel] = $logger;
        }

        return static::$_instances[$channel];
    }
}
