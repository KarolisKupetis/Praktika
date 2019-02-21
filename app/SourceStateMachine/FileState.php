<?php

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\database\Controller;
use App\Helper\FileReader;
use App\Helper\InputLoader;

class FileState implements StateInterface
{
    private $fileReader;
    private $patterns;
    private $hyphenator;
    private $DBController;

    public function __construct($filename)
    {
        $this->fileReader=new FileReader();
        $this->hyphenator= new Hyphenator();
        $this->DBController= new Controller();
        $this->patterns= $this->setPatterns($filename);
    }

    public function hyphenateWord($inputWord)
    {

        if ($this->isWordAlreadyHyphenated($inputWord)) {

            return $this->getAlreadyHyphenedWord($inputWord);
        }

        return $this->hyphenator->hyphenateWord($inputWord,$this->patterns);
    }

    public function getPatterns()
    {
        return $this->patterns;
    }

    private function setPatterns($fileName)
    {
         return $this->fileReader->readFile($fileName);

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

    private function getAlreadyHyphenedWord($inputWord)
    {
        $wordsID = $this->DBController->findWordsIDByWord($inputWord);

        return $this->DBController->findHyphenedWordByWordID($wordsID);
    }
}