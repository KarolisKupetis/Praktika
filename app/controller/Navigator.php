<?php

namespace App\Controller;

use App\database\Controller;
use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\SourceStateMachine\DatabaseState;
use App\SourceStateMachine\FileState;
use Monolog\Logger;


class Navigator
{
    private $inputLoader;
    private $fileReader;
    private $dbControll;
    private $source;
    private $HPController;

    public function __construct()
    {
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->dbControll = new Controller();
        $this->HPController = new HyphenationController();
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
            echo "  4. Change source\n";
            echo "  5. Terminate program \n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                    echo 'Type in the word you want to hyphenate: ';
                    $inputWord = $this->inputLoader->getUserInput();
                    $result = $this->HPController->hyphenateWord($inputWord);
                    echo 'Hyphenated word: ' . $result . "\n";
                    break;
                case 2:
                    $this->uiForSentencesHyphenation();
                    break;
                case 3:
                    $this->uiForDatabaseWork();
                    break;
                case 4:
                    $this->uiForSourceSelection();
                    break;
                case 5:
                    $run = false;
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
                    $sentence = $this->inputLoader->getUserInput();
                    $this->HPController->hyphenateSentence($sentence);
                    break;
                case 2:
                    $filename = $command[1];
                    $this->HPController->hyphenateFile($filename);
                    break;
                case 3:
                    $run = false;
            }

        }
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
            echo "  1  [filename.txt] -Use data from file \n";
            echo "  2  Use data from database\n";
            echo "  3. Go back to main menu \n";
            $userInput = trim($this->inputLoader->getUserInput());
            $command = explode(' ', $userInput);

            switch ($command[0]) {
                case 1:
                    $filename = $command[1];
                    $this->sourceSelection(1, $filename);
                    $run=false;
                    break;
                case 2:
                    $this->sourceSelection(2);
                    $run=false;
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function sourceSelection($option, $filename = null)
    {
        if ($option === 1) {
            $this->HPController->setState(new FileState($filename));
            $this->source='File';

        } elseif ($option === 2) {
           $this->HPController->setState(new DatabaseState());
            $this->source = 'Database';
        }
    }
}
