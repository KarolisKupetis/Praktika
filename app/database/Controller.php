<?php

namespace App\database;

use App\database\models\HyphenedWordsModel;
use App\database\models\Patterns_WordsModel;
use App\database\models\PatternsModel;
use App\database\models\PatternsWordsModel;

class Controller
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
        $this->patternsModel->truncatePatternsTable();
        $this->patternsModel->insertPatterns($patterns);
    }

    public function uploadWords($words)
    {
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

    public function saveWordHyphenation(array $patterns, $wordsTableRow,$hyphenedWord)
    {
        $wordsTableRow = $this->findWord($wordsTableRow);
        $this->hyphenedWordsModel->insertHyphenedWord($hyphenedWord,$wordsTableRow['ID']);
        foreach ($patterns as $pattern)
        {
            $tableRow =$this->patternsModel->getPatternByPattern($pattern);
            $this->patternsWordsModel->insertPatterns_Words($tableRow['ID'],$wordsTableRow['ID']);
        }
    }

    public function findHyphenedWordByWordID($wordId)
    {
        $tableRow = $this->hyphenedWordsModel->getHyphenedWordByWordID($wordId);
        $result = $tableRow['hyphened_word'];

        return $result;
    }
}