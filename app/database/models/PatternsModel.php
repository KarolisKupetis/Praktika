<?php

namespace App\database\models;

use App\database\AbstractModel;
use PDO;

class PatternsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection=$this->connect();
    }

    public function insertPatterns(array $patternsArray)
    {
        $this->connection=$this->connect();
        $sql = 'INSERT IGNORE INTO patterns(pattern) VALUES(?)';
        $insertStatement=$this->connection->prepare($sql);
        $this->connection->beginTransaction();

        foreach ($patternsArray as $pattern) {
            $insertStatement->execute([$pattern]);
        }

        $this->connection->commit();
    }

    public function getPatterns()
    {
       return $this->selectAll('patterns');
    }

    public function truncatePatternsTable()
    {
        $this->truncateTable('patterns');
    }

    public function getPatternByPattern($pattern)
    {
        $this->connection=$this->connect();
        $sql = 'SELECT * FROM patterns WHERE pattern= ? ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$pattern]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}