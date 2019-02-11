<?php

namespace App\Controller;

use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\helper\LoggerCreator;
use App\Helper\TimeTracker;

class Controller
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
        $this->logger=LoggerCreator::getInstance();
    }
    public function beginWork()
    {
        $syllables = $this->fileReader->readFile('tekstas.txt');
        $run = true;

        while ($run) {
            echo "1. Ivesti zodi ir ji suskiemenuoti\n";
            echo "2. Baigti darba\n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:

                    $inputLine=$this->getInput();

                    $result = $this->processInput($inputLine,$syllables);

                    $this->processResult($result);
                    break;
                case 2:
                    $run = false;
                    break;
            }

        }
    }

    private function getInput()
    {
        echo 'Zodis: ';
        $inputLine=$this->inputLoader->getUserInput();
        $this->timeTracker->startTrackingTime();
        $this->logger->addToMessage('Given word: '.$inputLine);

        return $inputLine;
    }

    private function processInput($inputLine, $syllables)
    {
        $result = $this->hyphenator->hyphenateWord($inputLine,$syllables);
        $this->timeTracker->endTrackingTime();

        return $result;
    }

    private function processResult($result)
    {
        echo 'Suskiemenuotas zodis: '.$result;

        $elapsedTime = $this->timeTracker->getElapsedTime();

        echo 'Trukme: '.$elapsedTime."\n\n";
        $this->logger->addToMessage('Hyphened word: '.$result.' Time took: '.$elapsedTime);
        $this->logger->logToFile();
        $this->logger->clearLogMessage();

    }
}
