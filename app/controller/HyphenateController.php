<?php

namespace App\Controller;

use App\Helper\DBConnector;
use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\helper\LoggerCreator;
use App\Helper\TimeTracker;
use Psr\Log\NullLogger;

class HyphenateController
{
    private $hyphenator;
    private $inputLoader;
    private $fileReader;
    private $timeTracker;
    private $logger;
    private $syllables;
    private $dbControll;
    private $source;

    public function __construct()
    {
        $this->hyphenator = new Hyphenator();
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->timeTracker = new TimeTracker();
        $this->logger = LoggerCreator::getInstance();
        $this->dbControll = new DBConnector();
        $this->syllables = $this->fileReader->readFile('patterns.txt');
    }

    public function beginWork()
    {
        $run = true;
        $this->source='Nepasirinktas';
        while ($run) {
            echo "\n Naudojamas skiemenavimo modeliu saltinis: $this->source \n";
            echo "  1. Ivesti zodi ir ji suskiemenuoti\n";
            echo "  2. Skiemenuoti sakini\n";
            echo "  3. Ikelti zodzius arba skiemenavimo modelius i duomenu baze\n";
            echo "  4. Baigti darba\n";
            echo "  5. Keisti skiemenavimo modeliu saltini \n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                   $this->uiForOneWordHyphenation();
                    break;
                case 2:
                    $this->uiForSentenceHyphenation();
                    break;
                case 3:
                    $this->uiForDatabaseWork();
                    break;
                case 4:
                    $run = false;
                    break;
                case 5:
                    $this->uiForSourceSelection();
                    break;
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

    private function uiForSentenceHyphenation()
    {
        $run = true;
        while ($run) {
            echo "  1. Ivesti sakini ir ji suskiemenuoti\n";
            echo "  2  [pavadinimas.txt] - suskiemenuoti failo turini\n";
            echo "  3. Grizti atgal \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                   $this->hyphenateFromCMD();
                    break;
                case 2:
                    $filename = $command[1];
                    $this->hyphenateFromFile($filename);
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
        $this->logInput(implode($fileContent));

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

    private function hyphenateFromCMD(){
        echo "\nIrasykite sakini: ";
        $sentence = $this->inputLoader->getUserInput();
        $this->logInput($sentence);
        $hyphenetedSentence = $this->hyphenateSentence($sentence);
        $this->logOutput($hyphenetedSentence);
        echo 'Isskiemenuotas sakinys: '.$hyphenetedSentence;
        $elapsedTime= $this->timeTracker->getElapsedTime();
        echo "\n" . 'Trukme : ' . $elapsedTime . "\n";
    }

    private function hyphenateFromFile($filename){
        $this->timeTracker->startTrackingTime();
        $result = "\n" . $this->hyphenateFile($filename);
        $this->timeTracker->endTrackingTime();
        $elapsedTime = $this->timeTracker->getElapsedTime();
        $this->logOutput($result);
        echo $result;
        echo "\n" . 'Trukme : ' . $elapsedTime . "\n";
    }

    private function uiForOneWordHyphenation()
    {
        echo 'Iveskite zodi: ';
        $inputLine = $inputLine = $this->inputLoader->getUserInput();
        $this->timeTracker->startTrackingTime();
        $result = $this->hyphenateOneWord($inputLine);
        $this->timeTracker->endTrackingTime();
        echo 'Suskiemenuotas zodis: ' . $result . "\n";
        echo 'Trukme: ' . $this->timeTracker->getElapsedTime() . "\n";
    }

    private function uiForDatabaseWork()
    {
        $run = true;
        while ($run) {
            echo "  1 [failopavadinimas.txt] -Ikelti skiemenu modelius \n";
            echo "  2 [failopavadinimas.txt] -Ikelti zodzius\n";
            echo "  3. Grizti atgal \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $patternsArray = $this->fileReader->readFile($command[1]);
                    $this->dbControll->uploadData($patternsArray,1);
                    echo "\n Duomenys ikelti \n";
                    break;
                case 2:
                    $arrayOfWords = $this->fileReader->readFile($command[1]);
                    $this->dbControll->uploadData($arrayOfWords,2);
                    echo "\n Duomenys ikelti \n";
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function uiForSourceSelection(){

        $run = true;
        while($run){
            echo "  1  [failopavadinimas.txt] -Naudoti duomenis is failo \n";
            echo "  2  Naudoti duomenis is duomenu bazes\n";
            echo "  3. Grizti atgal \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $filename = $command[1];
                    $this->sourceSelection(1,$filename);
                    break;
                case 2:
                    $this->sourceSelection(2);
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function sourceSelection($option,$filename=null)
    {
        if ($option === 1) {
            $this->syllables=$this->fileReader->readFile($filename);
            $this->source=$filename;

        }elseif($option === 2){
            $this->syllables=$this->dbControll->getData();
            $this->source='Database';

        }
    }
}
