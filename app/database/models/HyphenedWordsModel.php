<?php

namespace App\database\models;

use App\database\AbstractModel;
use PDO;

class HyphenedWordsModel extends AbstractModel
{
    public function __construct()
    {
        $this->connection=$this->connect();
    }

    public function insertHyphenedWord($hyphenedWord, $wordID) {
        $stmt = $this->connection->prepare('INSERT IGNORE INTO hyphened_words(hyphened_word,word_id) VALUES (?,?)');
        $stmt->execute([$hyphenedWord,$wordID]);
    }

    public function getHyphenedWordByID($id)
    {
       return $this->selectByID('hyphened_words',$id);
    }

    public function getHyphenedWordByWordID($id)
    {
        $this->connection=$this->connect();
        $sql = 'SELECT * FROM hyphened_words WHERE word_id= ? ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function truncateHyphened_wordsTable()
    {
        $this->truncateTable('hyphened_words');
    }
}