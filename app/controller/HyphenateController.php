<?php

namespace App\Controller;

use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\helper\LoggerCreator;
use App\Helper\TimeTracker;

class HyphenateController
{
    private $hyphenator;
    private $inputLoader;
    private $fileReader;
    private $timeTracker;
    private $logger;

    public function __construct()
    {
        $this->hyphenator = new Hyphenator();
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->timeTracker = new TimeTracker();
        $this->logger = LoggerCreator::getInstance();
    }

    public function hyphenateOneWord($word)
    {
        $syllables = $this->fileReader->readFile('tekstas.txt');
        $hyphenedWord = $this->hyphenator->hyphenateWord($word, $syllables);

        return $hyphenedWord;
    }

    public function beginWork()
    {
        $run = true;

        while ($run) {
            echo "\n  1. Ivesti zodi ir ji suskiemenuoti\n";
            echo "  2. Skiemenuoti sakini\n";
            echo "  3. Baigti darba\n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                    echo 'Iveskite zodi: ';
                    $inputLine = $inputLine = $this->inputLoader->getUserInput();
                    $this->timeTracker->startTrackingTime();
                    $result = $this->hyphenateOneWord($inputLine);
                    echo 'Suskiemenuotas zodis: ' . $result . "\n";
                    $this->timeTracker->endTrackingTime();
                    echo 'Trukme: ' . $this->timeTracker->getElapsedTime() . "\n";
                    break;
                case 2:
                    $this->hyphenateSentenceOrFile();
                    break;
                case 3:
                    $run = false;
                    break;
            }

        }
    }

    private function logInput($inputToLog)
    {
        $this->timeTracker->startTrackingTime();
        $this->logger->addToMessage('Given input: ' . implode($inputToLog));
    }

    private function logOutput($result)
    {
        $this->timeTracker->endTrackingTime();
        $elapsedTime = $this->timeTracker->getElapsedTime();
        $this->logger->addToMessage('Result:{ ' . $result . '} Time took:' . $elapsedTime);
        $this->logger->logToFile();
        $this->logger->clearLogMessage();
    }

    private function hyphenateSentenceOrFile()
    {
        $run = true;
        while ($run) {
            echo "  1. Ivesti sakini ir ji suskiemenuoti\n";
            echo "  2  [pavadinimas.txt] - suskiemenuoti failo turini\n";
            echo "  3. Grizti atgal \n";
            $userInput = trim($this->inputLoader->getUserInput());
            echo $userInput;
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    echo "\nIrasykite sakini: ";
                    $sentence = $this->inputLoader->getUserInput();
                    $this->logInput($sentence);
                    $hyphenetedSentence = $this->hyphenateSentence($sentence);
                    $this->logOutput($hyphenetedSentence);
                    echo 'Isskiemenuotas sakinys: '.$hyphenetedSentence;
                    $elapsedTime= $this->timeTracker->getElapsedTime();
                    echo "\n" . 'Trukme : ' . $elapsedTime . "\n";
                    break;
                case 2:
                    $this->timeTracker->startTrackingTime();
                    $result = "\n" . $this->hyphenateFile($command[1]);
                    $this->timeTracker->endTrackingTime();
                    $elapsedTime = $this->timeTracker->getElapsedTime();
                    $this->logOutput($result);
                    echo $result;
                    echo "\n" . 'Trukme : ' . $elapsedTime . "\n";
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function hyphenateFile($fileName)
    {
        $result = '';
        $fileContent = $this->fileReader->readFile($fileName);
        $this->logInput($fileContent);
        foreach ($fileContent as $sentence) {
            $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            foreach ($sentenceAsArray as &$element) {

                if ($this->isWord($element) === true) {
                    $element = $this->hyphenateOneWord($element);
                }
            }
            unset($element);
            $result .= implode($sentenceAsArray) . "\n";
        }

        return $result;
    }

    private function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        foreach ($sentenceAsArray as &$element) {

            if ($this->isWord($element) === true) {
                $element = $this->hyphenateOneWord($element);
            }
        }
        unset($element);

        return implode($sentenceAsArray) . "\n";
    }

    private function isWord($subject)
    {
        if (preg_match('/[a-zA-Z]/', $subject)) {

            return true;
        }

        return false;
    }
}
