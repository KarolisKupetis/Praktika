<?php

namespace App\Controller;

use App\database\Controller;
use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\Helper\TimeTracker;
use Monolog\Logger;


class HyphenateController
{
    private $hyphenator;
    private $inputLoader;
    private $fileReader;
    private $timeTracker;
    private $syllables;
    private $dbControll;
    private $logger;
    private $source;

    public function __construct()
    {
        $this->logger = new Logger("InputLogger");
        $this->hyphenator = new Hyphenator($this->logger);
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->timeTracker = new TimeTracker();
        $this->dbControll = new Controller();
        $this->syllables = $this->fileReader->readFile('patterns.txt');
    }

    public function beginWork()
    {
        $run = true;
        $this->source = 'Not selected';

        while ($run) {
            echo "\n Current source: $this->source \n";
            echo "  1. Type and hyphenate word.\n";
            echo "  2. Hyphenate more than one word\n";
            echo "  3. Upload patterns or words into database\n";
            echo "  4. Terminate program\n";
            echo "  5. Change usable source \n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                    $this->hyphenateWordFromCMD();
                    break;
                case 2:
                    $this->uiForSentencesHyphenation();
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

    private function uiForSentencesHyphenation()
    {
        $run = true;

        while ($run) {
            echo "  1. Type in and hyphenate sentence\n";
            echo "  2  [filename.txt] - hyphenate file content\n";
            echo "  3. Go back to main menu \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $this->hyphenateSentenceFromCMD();
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
        $fileContent = $this->fileReader->readFile($fileName);
        $result ='';
        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result.=$hyphenedSentence;
        }
        return $result;
    }

    private function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key => $element) {

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
        return $this->hyphenator->hyphenateWord($word, $this->syllables);
    }

    private function hyphenateSentenceFromCMD()
    {
        echo "\nType in the sentence you want to hyphenate: ";
        $sentence = $this->inputLoader->getUserInput();
        $hyphenetedSentence = $this->hyphenateSentence($sentence);
        echo 'Hyphenated sentence: ' . $hyphenetedSentence;
        $elapsedTime = $this->timeTracker->getElapsedTime();
        echo "\n" . 'Time taken : ' . $elapsedTime . "\n";
    }

    private function hyphenateFromFile($filename)
    {
        $this->timeTracker->startTrackingTime();
        $result = "\n" . $this->hyphenateFile($filename);
        $this->timeTracker->endTrackingTime();
        echo $result;
        $elapsedTime = $this->timeTracker->getElapsedTime();
        echo "\n" . 'Time taken : ' . $elapsedTime . "\n";
    }

    private function hyphenateWordFromCMD()
    {
        echo 'Type in the word you want to hyphenate: ';
        $inputLine = $inputLine = $this->inputLoader->getUserInput();

        $this->timeTracker->startTrackingTime();
        $result = $this->hyphenateOneWord($inputLine);
        $this->timeTracker->endTrackingTime();
        echo 'Hyphenated word: ' . $result . "\n";
        echo 'Time taken: ' . $this->timeTracker->getElapsedTime() . "\n";
    }

    private function uiForDatabaseWork()
    {
        $run = true;

        while ($run) {
            echo "  1 [filename.txt] - upload patterns into database \n";
            echo "  2 [filename.txt] - upload words into database\n";
            echo "  3. go back to main menu \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $patternsArray = $this->fileReader->readFile($command[1]);
                    $this->dbControll->uploadPatterns($patternsArray);
                    echo "\n Data uploaded successfully \n";
                    break;
                case 2:
                    $arrayOfWords = $this->fileReader->readFile($command[1]);
                    $this->dbControll->uploadWords($arrayOfWords);
                    echo "\n Data uploaded successfully \n";
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function uiForSourceSelection()
    {
        $run = true;

        while ($run) {
            echo "  1  [filename.txt] - Use data from file \n";
            echo "  2  Use data from database\n";
            echo "  3. Go back to main menu \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $filename = $command[1];
                    $this->sourceSelection(1, $filename);
                    break;
                case 2:
                    $this->sourceSelection(2);
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function sourceSelection($option, $filename = null)
    {
        if ($option === 1) {
            $this->syllables = $this->fileReader->readFile($filename);
            $this->source = $filename;

        } elseif ($option === 2) {
            $this->syllables = $this->dbControll->getAllPatterns();
            $this->source = 'Database';

        }
    }
}
