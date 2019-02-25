<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\QueryBuilder;
use PDO;

class PatternsWordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection = $this->connect();
        $this->tableName = 'patterns_words';
    }

    public function findPatternsIDByWordID($wordId)
    {
        $patterns = array();
        $sql = new QueryBuilder();
        $sql->
        select()->
        from($this->tableName)->
        where('word_id', '=', '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$wordId]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $patterns[] = $row;
        }

        return $patterns;
    }

    public function insertPatternsWords($patternId, $wordId)
    {

        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'pattern_id, word_id')->
        values('?,?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$patternId, $wordId]);
    }

    public function truncatePatternsWordsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function deleteRelationWhereWordID($wordID)
    {
        $this->deleteWhere($this->tableName, 'word_id', $wordID);
    }
}