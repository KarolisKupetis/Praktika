<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\QueryBuilder;
use PDO;

class PatternsWordsModel extends AbstractModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'patterns_words';
    }

    public function getPatternsIdsByWordID($wordId)
    {
        $patterns = array();

        $rows = $this->selectAllBy($this->tableName, 'word_id', $wordId);

        foreach ($rows as $row) {
            $patterns[] = $row['pattern_id'];
        }

        return $patterns;
    }

    public function insertOneRow($patternId, $wordId)
    {
        $sql = new QueryBuilder();
        $sql->
        insert($this->tableName, 'pattern_id, word_id')->
        values('?,?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$patternId, $wordId]);
    }

    public function truncatePatternsWordsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function deleteWhereWordID($wordID)
    {
        $this->deleteWhere($this->tableName, 'word_id', $wordID);
    }

    public function insertArrayOfPatternsWords(array $patterns, $wordId)
    {
        $sql = new QueryBuilder();
        $sql->
        insert($this->tableName, 'pattern_id, word_id')->
        values('?,?');

        foreach ($patterns as $pattern) {
            $this->insertOneRow($pattern, $wordId);
        }
    }


}