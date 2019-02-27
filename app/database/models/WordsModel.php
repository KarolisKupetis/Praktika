<?php

namespace App\database\models;

use App\database\AbstractModel;
use App\database\Connection;
use App\database\QueryBuilder;
use mysql_xdevapi\Exception;

class WordsModel extends AbstractModel
{
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
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
        try {

            foreach ($wordsArray as $row) {
                $insertStatement->execute([$row]);
            }
            $this->connection->commit();

        } catch (Exception $e) {
            $this->connection->rollBack();

            echo 'Failed to upload words';
        }

    }

    public function getWords()
    {
        $wordsRows = $this->selectAll('words');
        $words = array();

        foreach ($wordsRows as $wordRow) {
            $words[] = $wordRow['word'];
        }

        return $words;
    }

    public function getWordByID($id)
    {
        $tableRow = $this->getFirstOccurrenceWhere($this->tableName, 'ID', $id);

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
        $wordTableRow = $this->getFirstOccurrenceWhere($this->tableName, 'word', $word);

        return $wordTableRow['ID'];
    }

    public function updateWordWhereID($wordId, $newWord)
    {
        $sql = new QueryBuilder();
        $sql->
        update($this->tableName)->
        set('word = ?')->
        where('ID', '=', '?');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$newWord, $wordId]);
    }
}