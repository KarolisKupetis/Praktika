<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\QueryBuilder;
use PDO;

class HyphenedWordsModel extends AbstractModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'hyphened_words';
    }

    public function insertHyphenedWord($hyphenedWord, $wordID)
    {
        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'hyphened_word, word_id')->
        values('? , ?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$hyphenedWord, $wordID]);
    }

    public function truncateHyphenedWordsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function getAllHyphenedWords()
    {
        $hyphenatedWords = array();
        $rows =  $this->selectAll($this->tableName);

        foreach ($rows as $row)
        {
            $hyphenatedWords[] = $row['hyphened_word'];
        }

        return $hyphenatedWords;
    }

    public function deleteByID($id)
    {
        $this->deleteWhere($this->tableName, 'ID', $id);
    }

    public function getHyphenedWordById($id)
    {
        $tableRow =  $this->getFirstOccurrenceBy($this->tableName, 'ID', $id);

        return $tableRow['hyphened_word'];
    }

    public function getHyphenedWordByWordId($wordId)
    {
        $tableRow =  $this->getFirstOccurrenceBy($this->tableName, 'word_id', $wordId);

        return $tableRow['hyphened_word'];
    }
}