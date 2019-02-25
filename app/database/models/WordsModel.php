<?php

namespace App\database;

use PDO;

class WordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection = $this->connect();
        $this->tableName = 'words';
    }

    public function insertOneWord($word)
    {
        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'word')->
        values('?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($word);
    }

    public function insertWords(array $wordsArray)
    {
        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'word')->
        values('?');

        $insertStatement = $this->connection->prepare($sql);
        $this->connection->beginTransaction();

        foreach ($wordsArray as $row) {
            $insertStatement->execute([$row]);
        }

        $this->connection->commit();
    }

    public function getWords()
    {
        return $this->selectAll('words');
    }

    public function getWordByID($id)
    {
        return $this->selectBy($this->tableName, 'word_id', $id);
    }

    public function getWordByWord($word)
    {
        return $this->selectBy($this->tableName, 'word', $word);
    }

    public function truncateWordsTable()
    {
        $this->truncateTable('words');
    }

    public function deleteWordWhereID($wordID)
    {
        $this->deleteWhere($this->tableName, 'word_id', $wordID);
    }
}