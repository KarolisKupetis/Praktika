<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.20
 * Time: 10.40
 */

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\database\Controller;

class DatabaseState implements StateInterface
{
    private $DBController;
    private $patterns;
    private $hyphenator;

    public function __construct()
    {
        $this->DBController = new Controller();
        $this->hyphenator = new Hyphenator();
        $this->patterns=$this->DBController->getAllPatterns();
    }

    public function hyphenateWord($inputWord)
    {
        $this->patterns = $this->getPatterns();

        if ($this->isWordAlreadyHyphenated($inputWord)) {

            return $this->getAlreadyHyphenedWord($inputWord);
        }

        return $this->hyphenateNewWord($inputWord);
    }

    private function hyphenateNewWord($inputWord)
    {
        $hyphenedWord = $this->hyphenator->hyphenateWord($inputWord, $this->patterns);
        $usedPatterns = $this->hyphenator->getUsedPatterns();
        $usedPatterns = array_unique($usedPatterns);
        $this->DBController->saveWordHyphenation($usedPatterns, $inputWord, $hyphenedWord);
        $this->outputUsedPatterns($usedPatterns);

        return $hyphenedWord;
    }

    public function getPatterns()
    {
        return $this->patterns;
    }

    private function isWordAlreadyHyphenated($word)
    {
        $wordTableRow = $this->DBController->findWord($word);

        if ($wordTableRow) {
            $hyphenedWordTableRow = $this->DBController->findHyphenedWordByWordID($wordTableRow['ID']);

            if ($hyphenedWordTableRow) {

                return true;
            }
        }

        return false;
    }

    private function outputUsedPatterns($patterns)
    {
        echo 'Used patterns:';

        foreach ($patterns as $pattern) {
            echo $pattern . ' ';
        }

        echo "\n";
    }

    private function getUsedPatternsFromDB($wordID)
    {
        $usedPatterns = $this->DBController->findUsedPatternsWithWord($wordID);
        $this->outputUsedPatterns($usedPatterns);
    }

    private function getAlreadyHyphenedWord($inputWord)
    {
        $wordsID = $this->DBController->findWordsIDByWord($inputWord);
        $hyphenedWord = $this->DBController->findHyphenedWordByWordID($wordsID);
        $this->getUsedPatternsFromDB($wordsID);

        return $hyphenedWord;
    }

}