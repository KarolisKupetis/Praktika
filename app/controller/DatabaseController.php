<?php

namespace App\database;

use App\database\models\HyphenedWordsModel;
use App\database\models\PatternsModel;
use App\database\models\PatternsWordsModel;

class DatabaseController
{
    private $hyphenedWordsModel;
    private $patternsWordsModel;
    private $patternsModel;
    private $wordsModel;

    public function __construct()
    {
        $this->hyphenedWordsModel = new HyphenedWordsModel();
        $this->patternsWordsModel = new PatternsWordsModel();
        $this->patternsModel = new PatternsModel();
        $this->wordsModel = new WordsModel();
    }

    public function getAllWords()
    {
        $words = array();
        $table = $this->wordsModel->getWords();

        foreach ($table as $item) {
            $words[] = $item['word'];
        }

        return $words;
    }

    public function uploadPatterns($patterns)
    {
        $this->hyphenedWordsModel->truncateHyphened_wordsTable();
        $this->patternsWordsModel->truncatePatternsWordsTable();
        $this->patternsModel->truncatePatternsTable();
        $this->patternsModel->insertPatterns($patterns);
    }

    public function uploadWords($words)
    {
        $this->hyphenedWordsModel->truncateHyphened_wordsTable();
        $this->patternsWordsModel->truncatePatternsWordsTable();
        $this->wordsModel->truncateWordsTable();
        $this->wordsModel->insertWords($words);
    }

    public function getAllPatterns()
    {
        $patterns = array();
        $table = $this->patternsModel->getPatterns();

        foreach ($table as $item) {
            $patterns[] = $item['pattern'];
        }

        return $patterns;
    }

    public function findWord($word)
    {
        return $this->wordsModel->getWordByWord($word);
    }

    public function findWordsIDByWord($word)
    {
        $wordTableRow=$this->wordsModel->getWordByWord($word);
        return $wordTableRow['ID'];
    }

    public function saveWordHyphenation(array $patterns, $inputWord,$hyphenedWord)
    {
        $wordsID = $this->findWordsIDByWord($inputWord);

        if(!$wordsID)
        {
            $this->wordsModel->insertOneWord($inputWord);
            $wordsID = $this->findWordsIDByWord($inputWord);
        }

        $this->hyphenedWordsModel->insertHyphenedWord($hyphenedWord,$wordsID);

        foreach ($patterns as $pattern)
        {
            $PatternTableRow =$this->patternsModel->getPatternByPattern($pattern);
            $this->patternsWordsModel->insertPatternsWords($PatternTableRow['ID'],$wordsID);
        }
    }

    public function findHyphenedWordByWordID($wordId)
    {
        $tableRow = $this->hyphenedWordsModel->getHyphenedWordByWordID($wordId);
        $result = $tableRow['hyphened_word'];

        return $result;
    }

    public function findUsedPatternsWithWord($word)
    {
        $patterns = array();
        $tableRow = $this->wordsModel->getWordByWord($word);
        $wordID = $tableRow['ID'];
        $patternsRows = $this->patternsWordsModel->findPatternsIDByWordID($wordID);

        foreach ($patternsRows as $pattern){
            $tableRow =$this->patternsModel->getPatternByPatternID($pattern['pattern_id']);
            $patterns[] = $tableRow['pattern'];
        }

        return $patterns;
    }

    public function getHyphenedWords()
    {
        $hyphenedWords=array();
        $table =$this->hyphenedWordsModel->getAllHyphenedWords();

        foreach ($table as $item) {
            $hyphenedWords[] = $item['hyphened_word'];
        }

        return $hyphenedWords;
    }

    public function deleteWordWhereID($wordId)
    {
        $this->wordsModel->deleteWordWhereID($wordId);
        $tableRow = $this->hyphenedWordsModel->getHyphenedWordByWordID($wordId);
        $hyphenedWordID = $tableRow['ID'];
        $this->hyphenedWordsModel->deleteHyphenedWordWhereID($hyphenedWordID);
        $this->patternsWordsModel->deleteRelationWhereWordID($wordId);
    }

    public function insertOneWord($word)
    {
        $this->wordsModel->insertOneWord($word);
    }
}