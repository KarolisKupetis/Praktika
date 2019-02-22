<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.20
 * Time: 10.40
 */

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\database\DatabaseController;
use App\Helper\FileReader;
use Psr\Log\LoggerInterface;

class DatabaseState implements StateInterface
{
    private $DBController;
    private $patterns;
    private $hyphenator;
    private $logger;
    private $fileReader;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->DBController = new DatabaseController();
        $this->hyphenator = new Hyphenator();
        $this->fileReader= new FileReader();
        $this->patterns = $this->DBController->getAllPatterns();
    }

    public function hyphenateWord($inputWord)
    {
        if ($this->isWordAlreadyHyphenated($inputWord)) {
            $this->outputUsedPatterns($inputWord);

            return $this->getAlreadyHyphenedWord($inputWord);
        }

        $hyphenedWord= $this->hyphenateNewWord($inputWord);
        $this->outputUsedPatterns($inputWord);
        return $hyphenedWord;
    }

    public function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key => $element) {

            if ($this->isWord($element) === true) {
                $hyphenedSentence[$key] = $this->hyphenateOneWordFromSentence($element);
            }
        }

        $result = implode($hyphenedSentence) . "\n";
        $this->logger->info('Hyphened sentence' . $result);

        return $result;
    }

    public function hyphenateFile($fileName)
    {
        $fileContent = $this->fileReader->readFile($fileName);
        $result = '';

        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result .= $hyphenedSentence;
        }

        return $result;
    }

    private function hyphenateNewWord($inputWord)
    {
        $hyphenedWord = $this->hyphenator->hyphenateWord($inputWord, $this->patterns);
        $usedPatterns = $this->hyphenator->getUsedPatterns();
        $usedPatterns = array_unique($usedPatterns);
        $this->DBController->saveWordHyphenation($usedPatterns, $inputWord, $hyphenedWord);

        return $hyphenedWord;
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

    private function outputUsedPatterns($word)
    {
        $usedPatterns = $this->DBController->findUsedPatternsWithWord($word);

        echo 'Used patterns:';

        foreach ($usedPatterns as $pattern) {
            echo $pattern . ' ';
            $this->logger->info('used patter:' . $pattern);
        }

        echo "\n";
    }

    private function getAlreadyHyphenedWord($inputWord)
    {
        $wordsID = $this->DBController->findWordsIDByWord($inputWord);
        $hyphenedWord = $this->DBController->findHyphenedWordByWordID($wordsID);

        return $hyphenedWord;
    }

    private function isWord($subject)
    {
        if (preg_match('/[^a-zA-Z]/', $subject)) {

            return false;
        }

        return true;
    }

    private function hyphenateOneWordFromSentence($word)
    {
        return $this->hyphenator->hyphenateWord($word, $this->patterns);
    }
}