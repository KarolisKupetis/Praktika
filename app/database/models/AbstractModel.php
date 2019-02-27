<?php

namespace App\database;

use PDO;

abstract class AbstractModel
{
    protected $connection;
    protected $tableName;

    protected function __construct(Connection $dbConnection)
    {
        $this->connection = $dbConnection->getConnection();
    }

    protected function selectAll($tableName)
    {
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($tableName);

        $stmt = $this->connection->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    protected function getFirstOccurrenceWhere($tableName, $searchSubject, $searchValue, $condition = '=')
    {
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($tableName)->
        where($searchSubject, $condition, '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$searchValue]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    protected function selectAllWhere($tableName, $searchSubject, $searchValue, $condition = '=')
    {
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($tableName)->
        where($searchSubject, $condition, '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$searchValue]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    protected function truncateTable($tableName)
    {
        $sql = new QueryBuilder();
        $sql->
        truncate($tableName);

        $this->connection->exec($sql);
    }

    protected function deleteWhere($tableName, $fieldBy, $fieldValue, $condition = '=')
    {

        $sql = new QueryBuilder();
        $sql->
        delete()->
        from($tableName)->
        where($fieldBy, $condition, '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$fieldValue]);
    }
}