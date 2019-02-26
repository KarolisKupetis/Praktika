<?php

namespace App\Controller;

use App\Helper\TimeTracker;
use App\SourceStateMachine\DatabaseState;
use Psr\Log\LoggerInterface;

class HyphenationController
{
    private $timeTracker;
    private $state;
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger=$logger;
        $this->timeTracker= new TimeTracker();
        $this->state= new DatabaseState($logger);
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



}