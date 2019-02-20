<?php

namespace App\database;

use PDO;

class WordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection=$this->connect();
    }

    public function insertOneWord($word) {
            $stmt = $this->connection->prepare('INSERT IGNORE INTO words(word) VALUES (:word)');
            $params = array('word'  =>$word);
            $stmt->execute($params);
    }

    public function insertWords(array $wordsArray)
    {
        $sql = 'INSERT IGNORE INTO words(word) VALUES(?)';
        $insertStatement=$this->connection->prepare($sql);
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
       return $this->selectByID('words',$id);
    }

    public function getWordByWord($word)
    {
        $this->connection=$this->connect();
        $sql = 'SELECT * FROM words WHERE word= ? ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$word]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function truncateWordsTable()
    {
        $this->truncateTable('words');
    }

}