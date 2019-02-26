<?php

namespace App\Controller;

use App\database\models\HyphenedWordsModel;
use App\database\models\PatternsModel;
use App\database\models\PatternsWordsModel;
use App\database\WordsModel;
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->timeTracker = new TimeTracker();
        $this->fileReader = new FileReader();
        $this->state = new DatabaseState($logger);
        $this->wordsModel = new WordsModel();
        $this->patternsModel = new PatternsModel();
        $this->patternsWordsModel = new PatternsWordsModel();
        $this->hyphenatedWordsModel = new HyphenedWordsModel();
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