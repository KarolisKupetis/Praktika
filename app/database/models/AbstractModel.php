<?php

namespace App\database;

use PDO;

abstract class AbstractModel
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = 'qwer';
    private $dbname = 'hyphenator';
    protected $connection;

    protected function connect()
    {
        $this->connection = null;

        try {
            $source = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
            $this->connection = new \PDO($source, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Connection error: ' . $e->getMessage();
        }

        return $this->connection;
    }

    protected function selectAll($tableName)
    {
        $this->connection = $this->connect();
        $words = array();
        $sql = 'SELECT * FROM' . " $tableName";
        $stmt = $this->connection->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $words[] = $row;
        }

        return $words;
    }

    protected function selectByID($tableName, $id)
    {
        $this->connection = $this->connect();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id= ? ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    protected function truncateTable($tableName)
    {
        $this->connection = $this->connect();
        $sql = 'TRUNCATE TABLE ' . $tableName . '';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$tableName]);
    }

    protected function deleteWhereID($tableName, $ID)
    {
        $this->connection = $this->connect();
        $sql = 'DELETE FROM ' . $tableName . ' WHERE ID= ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$ID]);
    }

}