<?php

namespace App\helper;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerCreator
{
    public static $instance;
    private $logMessage;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new LoggerCreator();
        }

        return self::$instance;
    }

    public function logToFile()
    {
        $log = new Logger('my_app');
        $log->pushHandler(new StreamHandler('logger.txt',Logger::DEBUG));
        $log ->info($this->logMessage);
    }

    public function addToMessage($string)
    {
        $this->logMessage.=$string;
    }

    public function getLogMessage()
    {
        return $this->logMessage;
    }

    public function clearLogMessage()
    {
        $this->logMessage='';
    }

}