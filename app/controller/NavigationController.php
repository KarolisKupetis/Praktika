<?php

namespace App\Controller;

use App\database\Connection;
use App\Helper\InputLoader;
use App\Helper\TimeTracker;
use App\SourceStateMachine\DatabaseState;
use App\SourceStateMachine\FileState;
use Psr\Log\LoggerInterface;

class NavigationController
{
    private $inputLoader;
    private $sourceName;
    private $hyphenatorController;
    private $logger;
    private $dbController;
    private $timeTracker;

    public function __construct(LoggerInterface $logger, Connection $dbController)
    {
        $this->dbController = $dbController;
        $this->logger = $logger;
        $this->inputLoader = new InputLoader();
        $this->hyphenatorController = new HyphenationController($logger, $dbController);
        $this->timeTracker = new TimeTracker();
    }

    public function beginWork()
    {
        $run = true;
        $this->sourceName = 'Database';

        while ($run) {
            echo "\n Current source: $this->sourceName \n";
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
                    $this->logger->info('Given word :' . $inputWord);
                    $result = $this->hyphenatorController->hyphenateWord($inputWord);
                    $this->logger->info('Hyphened word :' . $result);
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
                    echo 'Type in sentence :';
                    $sentence = $this->inputLoader->getUserInput();
                    $this->logger->info('Given sentence' . $sentence);
                    $resultSentence = $this->hyphenatorController->hyphenateSentence($sentence);
                    echo $resultSentence;
                    break;
                case 2:
                    $filename = $command[1];
                    $this->timeTracker->startTrackingTime();
                    $result = $this->hyphenatorController->hyphenateFile($filename);
                    $this->timeTracker->endTrackingTime();
                    echo $this->timeTracker->getElapsedTime();
                    //print_r($result);  // uncomment to see results. Commented just for faster results.
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
                    if ($command[1]) {
                        $this->hyphenatorController->uploadPatterns($command[1]);
                        echo "\n Data uploaded successfully \n";
                    } else {
                        echo "\n Specify filename \n";
                    }
                    break;
                case 2:
                    if ($command[1]) {
                        $this->hyphenatorController->uploadWords($command[1]);
                        echo "\n Data uploaded successfully \n";
                    } else {
                        echo "\n Specify filename \n";
                    }
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
                    $run = false;
                    break;
                case 2:
                    $this->sourceSelection(2);
                    $run = false;
                    break;
                case 3:
                    $run = false;
            }

        }
    }

    private function sourceSelection($option, $filename = null)
    {
        if ($option === 1) {
            $this->hyphenatorController->setState(new FileState($filename, $this->logger));
            $this->sourceName = 'File';

        } elseif ($option === 2) {
            $this->hyphenatorController->setState(new DatabaseState($this->logger, $this->dbController));
            $this->sourceName = 'Database';
        }
    }
}
