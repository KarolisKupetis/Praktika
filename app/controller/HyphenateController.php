<?php

namespace App\Controller;

use App\Helper\DBController;
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
    private $syllables;
    private $dbControll;
    public function __construct()
    {
        $this->hyphenator = new Hyphenator();
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->timeTracker = new TimeTracker();
        $this->logger = LoggerCreator::getInstance();
        $this->dbControll = new DBController();
        $this->syllables = $this->fileReader->readFile('patterns.txt');
    }



    public function beginWork()
    {
        $run = true;

        while ($run) {
            echo "\n  1. Ivesti zodi ir ji suskiemenuoti\n";
            echo "  2. Skiemenuoti sakini\n";
            echo "  3. Baigti darba\n";
            echo "  4. DB\n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                    echo 'Iveskite zodi: ';
                    $inputLine = $inputLine = $this->inputLoader->getUserInput();
                    $this->timeTracker->startTrackingTime();
                    $result = $this->hyphenateOneWord($inputLine);
                    $this->timeTracker->endTrackingTime();
                    echo 'Suskiemenuotas zodis: ' . $result . "\n";
                    echo 'Trukme: ' . $this->timeTracker->getElapsedTime() . "\n";
                    break;
                case 2:
                    $this->hyphenateSentenceOrFile();
                    break;
                case 3:
                    $run = false;
                    break;
                case 4:
                    $this->dbControll->connect();
            }

        }
    }

    private function logInput($inputToLog)
    {
        $this->timeTracker->startTrackingTime();
        $this->logger->addToMessage('Given input: ' . $inputToLog);
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
        $hyphenedFile = '';
        $fileContent = $this->fileReader->readFile($fileName);
        $this->logInput($fileContent);

        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $hyphenedFile .= $hyphenedSentence;
        }

        return $hyphenedFile;
    }

    private function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z0-9])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key=>$element) {

            if ($this->isWord($element) === true) {
                $hyphenedSentence[$key] = $this->hyphenateOneWord($element);
            }
        }

        return implode($hyphenedSentence) . "\n";
    }

    private function isWord($subject)
    {
        if (preg_match('/[^a-zA-Z]/', $subject)) {

            return false;
        }

        return true;
    }

    public function hyphenateOneWord($word)
    {
        $hyphenedWord = $this->hyphenator->hyphenateWord($word,$this->syllables );
        return $hyphenedWord;
    }

}
