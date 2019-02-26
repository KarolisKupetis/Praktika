<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.26
 * Time: 14.01
 */

namespace App\database;

use PDO;

class Connection
{
    private static $instance ;
    private $connection;

    private $host = 'localhost';
    private $user = 'root';
    private $password = 'qwer';
    private $databaseName = 'hyphenator';

    private function __construct()
    {
        try {
            $source = 'mysql:host=' . $this->host . ';dbname=' . $this->databaseName;
            $this->connection = new \PDO($source, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {

            echo 'Connection error: ' . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new Connection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}