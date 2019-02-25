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
    protected $tableName;

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
        $rows = array();
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($tableName);

        $stmt = $this->connection->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    protected function selectBy($tableName,$searchSubject,$searchValue)
    {
        $this->connection = $this->connect();
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($tableName)->
        where('?', '=', '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$searchSubject,$searchValue]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function truncateTable($tableName)
    {
        $sql = new QueryBuilder();
        $sql->
        truncate($tableName);

        $this->connection = $this->connect();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$tableName]);
    }

    protected function deleteWhere($tableName,$fieldBy, $fieldValue,$condition='=')
    {
        $this->connection = $this->connect();
        $sql = new QueryBuilder();
        $sql->
        delete()->
        from($tableName)->
        where('?','?', '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$fieldBy,$condition,$fieldValue]);
    }

}