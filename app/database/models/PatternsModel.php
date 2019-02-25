<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\QueryBuilder;
use PDO;

class PatternsModel extends AbstractModel
{

    public function __construct()
    {
        $this->connection = $this->connect();
        $this->tableName = 'patterns';
    }

    public function insertPatterns(array $patternsArray)
    {
        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'pattern')->
        values('?');

        $insertStatement = $this->connection->prepare($sql);
        $this->connection->beginTransaction();

        foreach ($patternsArray as $pattern) {
            $insertStatement->execute([$pattern]);
        }

        $this->connection->commit();
    }

    public function getPatterns()
    {
        return $this->selectAll($this->tableName);
    }

    public function truncatePatternsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function getPatternBy($pattern = null, $patternId = null)
    {
        if ($pattern) {
            return $this->selectBy($this->tableName, 'pattern', $pattern);
        } elseif ($patternId) {
            return $this->selectBy($this->tableName, 'ID', $patternId);
        }

        return null;
    }
}