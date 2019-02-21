<?php

namespace App\Controller;

use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\Helper\TimeTracker;
use App\SourceStateMachine\NoState;
use Psr\Log\LoggerInterface;

class HyphenationController
{
    private $timeTracker;
    private $inputLoader;
    private $fileReader;
    private $state;
    private $hyphenator;
    private $patterns;

    public function __construct()
    {
        $this->timeTracker= new TimeTracker();
        $this->inputLoader= new InputLoader();
        $this->fileReader= new FileReader();
        $this->hyphenator = new Hyphenator();
        $this->state= new NoState();
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

        return implode($hyphenedSentence) . "\n";
    }

    public function hyphenateFile($fileName)
    {
        $fileContent = $this->fileReader->readFile($fileName);
        $result = '';

        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result .= $hyphenedSentence;
        }

        echo $result;
    }

    public function hyphenateWord($inputword)
    {
        return $this->state->hyphenateWord($inputword);
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

    public function setState($sourceState)
    {
        if ($sourceState === $this->state) {
            echo "Already selected";

        } else {
            $this->state = $sourceState;
            $this->patterns=$this->state->getPatterns();
        }
    }



}