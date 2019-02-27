<?php

namespace App\Controller;

use App\database\Connection;
use App\database\models\HyphenedWordsModel;
use App\database\models\PatternsModel;
use App\database\models\PatternsWordsModel;
use App\database\models\WordsModel;
use App\Helper\FileReader;
use App\Helper\TimeTracker;
use App\SourceStateMachine\DatabaseState;
use Psr\Log\LoggerInterface;

class HyphenationController
{
    private $timeTracker;
    private $state;
    private $logger;
    private $fileReader;
    private $patternsModel;
    private $wordsModel;
    private $hyphenatedWordsModel;
    private $patternsWordsModel;

    public function __construct(LoggerInterface $logger, Connection $dbConnection)
    {
        $this->logger = $logger;
        $this->timeTracker = new TimeTracker();
        $this->fileReader = new FileReader();
        $this->state = new DatabaseState($logger,$dbConnection);
        $this->wordsModel = new WordsModel($dbConnection);
        $this->patternsModel = new PatternsModel($dbConnection);
        $this->patternsWordsModel = new PatternsWordsModel($dbConnection);
        $this->hyphenatedWordsModel = new HyphenedWordsModel($dbConnection);
    }

    public function hyphenateWord($inputWord)
    {
        return $this->state->hyphenateWord($inputWord);
    }

    public function hyphenateSentence($sentence)
    {
        return $this->state->hyphenateSentence($sentence);
    }

    public function hyphenateFile($filename)
    {
        return $this->state->hyphenateFile($filename);
    }

    public function setState($sourceState)
    {
        $this->state = $sourceState;
    }

    public function uploadPatterns($filename)
    {
        $patternsArray = $this->fileReader->readFile($filename);
        $this->patternsModel->truncatePatternsTable();
        $this->patternsWordsModel->truncatePatternsWordsTable();
        $this->hyphenatedWordsModel->truncateHyphenedWordsTable();
        $this->patternsModel->insertPatterns($patternsArray);
    }

    public function uploadWords($filename)
    {
        $arrayOfWords = $this->fileReader->readFile($filename);
        $this->wordsModel->truncateWordsTable();
        $this->patternsWordsModel->truncatePatternsWordsTable();
        $this->hyphenatedWordsModel->truncateHyphenedWordsTable();
        $this->wordsModel->insertWords($arrayOfWords);
    }


}