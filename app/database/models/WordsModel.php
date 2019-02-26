<?php

namespace App\database;

use PDO;

class WordsModel extends AbstractModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'words';
    }

    public function insertOneWord($word)
    {
        $sql = new QueryBuilder();
        $sql->
        insertIgnore($this->tableName, 'word')->
        values('?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$word]);
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
        $rows = $this->selectAll('words');
        $words = array();

        foreach ($rows as $row)
        {
            $words[]=$row['word'];
        }

        return $words;
    }

    public function getWordByID($id)
    {
        $tableRow = $this->getFirstOccurrenceBy($this->tableName, 'ID', $id);

        return $tableRow['word'];
    }

    public function truncateWordsTable()
    {
        $this->truncateTable('words');
    }

    public function deleteWordWhereID($wordID)
    {
        $this->deleteWhere($this->tableName, 'ID', $wordID);
    }

    public function getWordIdByWord($word)
    {
        $wordTableRow = $this->getFirstOccurrenceBy($this->tableName, 'word', $word);

        return $wordTableRow['ID'];
    }

    public function updateWordWhereID($wordId,$newWord)
    {
        $sql = new QueryBuilder();
        $sql ->
        update($this->tableName)->
        set('word = ?')->
        where('ID','=','?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$newWord,$wordId]);
    }
}