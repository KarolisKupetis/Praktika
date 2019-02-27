<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\Connection;
use App\database\QueryBuilder;

class PatternsModel extends AbstractModel
{
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
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

    public function getAllPatterns()
    {
        $rows = $this->selectAll($this->tableName);
        $patterns = array();

        foreach ($rows as $row) {
            $patterns[] = $row['pattern'];
        }

        return $patterns;
    }

    public function truncatePatternsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function getPatternByID($patternId)
    {
        $tableRow = $this->getFirstOccurrenceWhere($this->tableName, 'ID', $patternId);

        return $tableRow['pattern'];
    }

    public function getPatternIDByPattern($pattern)
    {
        $tableRow = $this->getFirstOccurrenceWhere($this->tableName, 'pattern', $pattern);

        return $tableRow['ID'];
    }
}