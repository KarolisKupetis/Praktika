<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\QueryBuilder;
use PDO;

class HyphenedWordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection = $this->connect();
        $this->tableName = 'hyphened_words';
    }

    public function insertHyphenedWord($hyphenedWord, $wordID)
    {

        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'hyphened_word,word_id')->
        values('?,?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$hyphenedWord, $wordID]);
    }

    public function truncateHyphened_wordsTable()
    {
        $this->truncateTable($this->tableName);
    }

    public function getAllHyphenedWords()
    {
        return $this->selectAll($this->tableName);
    }

    public function deleteByID($id)
    {
        $this->deleteWhere($this->tableName, 'ID', $id);
    }

    public function getHyphenedWordBy($id = null, $wordId = null)
    {
        if ($id) {
            return $this->selectBy($this->tableName, 'id', $id);

        } elseif ($wordId) {

            return $this->selectBy($this->tableName, 'word_id', $wordId);
        }

        return null;
    }
}