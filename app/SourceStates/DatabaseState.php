<?php

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\database\Connection;
use App\database\models\HyphenedWordsModel;
use App\database\models\PatternsModel;
use App\database\models\PatternsWordsModel;
use App\database\WordsModel;
use App\Helper\FileReader;
use Psr\Log\LoggerInterface;

class DatabaseState implements StateInterface
{
    private $patterns;
    private $hyphenator;
    private $logger;
    private $fileReader;
    private $wordsModel;
    private $patternsModel;
    private $patternsWordsModel;
    private $hyphenedWordsModel;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->hyphenator = new Hyphenator();
        $this->fileReader = new FileReader();
        $this->wordsModel = new WordsModel();
        $this->patternsModel = new PatternsModel();
        $this->patternsWordsModel = new PatternsWordsModel();
        $this->hyphenedWordsModel = new HyphenedWordsModel();
        $this->patterns = $this->patternsModel->getAllPatterns();
    }

    public function hyphenateWord($inputWord)
    {
        if ($this->isWordAlreadyHyphenated($inputWord)) {

            echo $this->outputUsedPatterns($inputWord);
            return $this->getAlreadyHyphenatedWord($inputWord);
        }

        $hyphenedWord = $this->hyphenateNewWord($inputWord);

        echo $this->outputUsedPatterns($inputWord);

        return $hyphenedWord;
    }

    public function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenatedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key => $element) {

            if ($this->isWord($element) === true) {
                $hyphenatedSentence[$key] = $this->hyphenateOneWordFromSentence($element);
            }
        }

        $result = implode($hyphenatedSentence) . "\n";
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

        $this->saveHyphenationToDB($usedPatterns, $inputWord, $hyphenedWord);

        return $hyphenedWord;
    }

    private function isWordAlreadyHyphenated($word)
    {
        $wordID = $this->wordsModel->getWordIdByWord($word);

        if ($wordID) {
            $hyphenatedWordId = $this->hyphenedWordsModel->getHyphenedWordByWordId($wordID);

            if ($hyphenatedWordId) {

                return true;
            }
        }

        return false;
    }

    private function outputUsedPatterns($word)
    {
        $wordId = $this->wordsModel->getWordIdByWord($word);
        $usedPatternsIds = $this->patternsWordsModel->getPatternsIdsByWordID($wordId);
        $result = 'Used patterns are :';

        foreach ($usedPatternsIds as $id) {
            $pattern = $this->patternsModel->getPatternByID($id);
            $result .= ' ' . $pattern;
            $this->logger->info('used patter:' . $pattern);
        }

        $result.="\n";

        return $result;
    }

    private function getAlreadyHyphenatedWord($inputWord)
    {
        $wordsID = $this->wordsModel->getWordIdByWord($inputWord);
        $hyphenatedWord = $this->hyphenedWordsModel->getHyphenedWordByWordId($wordsID);

        return $hyphenatedWord;
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

    private function saveHyphenationToDB($usedPatterns, $inputWord, $hyphenedWord)
    {
        $connection = Connection::getInstance()->getConnection();
        $connection->beginTransaction();

        $wordId = $this->insertWord($inputWord);
        $this->insertPatternsWords($usedPatterns,$wordId);
        $this->hyphenedWordsModel->insertHyphenedWord($hyphenedWord, $wordId);

        $connection->commit();
    }

    private function insertPatternsWords($usedPatterns,$wordId)
    {
        $usedPatternsIds = array();

        foreach ($usedPatterns as $pattern)
        {
            $id = $this->patternsModel->getPatternIDByPattern($pattern);
            $usedPatternsIds[]=$id;
        }

        $this->patternsWordsModel->insertArrayOfPatternsWords($usedPatternsIds,$wordId);
    }

    private function insertWord($word)
    {
        $wordId = $this->wordsModel->getWordIdByWord($word);

        if (!$wordId) {
            $this->wordsModel->insertOneWord($word);
            $wordId = $this->wordsModel->getWordIdByWord($word);
        }

        return $wordId;
    }
}