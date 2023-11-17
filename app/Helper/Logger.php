<?php 

namespace Yosev\Login\Management\Helper;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\MemoryUsageProcessor;

class Logger
{
    private static ?MonologLogger $logger = null;

    public static function getLogger($classname): MonologLogger
    {
        if (!self::$logger) {
            self::$logger = new MonologLogger($classname);
            self::$logger->pushHandler(new RotatingFileHandler(__DIR__ . "/../app.log", 3, MonologLogger::INFO));
            $handlerJSON = new StreamHandler(__DIR__ . "/../app.log");
            $handlerJSON->setFormatter(new JsonFormatter());
            self::$logger->pushHandler($handlerJSON);
            self::$logger->pushHandler(new StreamHandler("php://stdout", MonologLogger::INFO));
            self::$logger->pushProcessor(new MemoryUsageProcessor());
            self::$logger->pushProcessor(new GitProcessor());
            self::$logger->pushProcessor(new HostnameProcessor());
            self::$logger->pushProcessor(function ($record) {
                $record['extra']['information'] = [
                    "app" => "Login Management",
                    "author" => "Yosev",
                    "version" => "1.0.0"  
                ];

                return $record;
            });
        }

        return self::$logger;
    }
}
