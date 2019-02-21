<?php

namespace App\database\models;

use App\database\AbstractModel;
use PDO;

class PatternsWordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection=$this->connect();
    }

    public function findPatternsIDByWordID($wordId)
    {
        $patterns=array();
        $this->connection=$this->connect();
        $sql = 'SELECT * FROM patterns_words WHERE word_id= ? ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$wordId]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $patterns[] = $row;
        }
        return $patterns;
    }

    public function insertPatternsWords($patternId,$wordId)
    {
        $sql = 'INSERT IGNORE INTO patterns_words(pattern_id,word_id) VALUES (?,?)';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$patternId,$wordId]);
    }

    public function truncatePatternsWordsTable()
    {
        $this->truncateTable('patterns_words');
    }

    public function deleteRelationWhereWordID($wordID)
    {
        $this->connection = $this->connect();
        $sql = 'DELETE FROM patterns_words WHERE word_id = ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$wordID]);
    }
}