<?php

namespace App\Controller;

use App\Helper\FileReader;
use App\Helper\InputLoader;
use App\Helper\TimeTracker;

class Controller
{
    private $hyphenator;
    private $inputLoader;
    private $fileReader;
    private $timeTracker;

    function beginWork()
    {
        $this->hyphenator = new Hyphenator();
        $this->inputLoader = new InputLoader();
        $this->fileReader = new FileReader();
        $this->timeTracker = new TimeTracker();
        $syllables = $this->fileReader->readFile('tekstas.txt');
        $run = true;

        while ($run) {
            echo "1. Ivesti zodi ir ji suskiemenuoti\n";
            echo "2. Baigti darba\n";
            $inputLine = $this->inputLoader->getUserInput();

            switch ($inputLine) {
                case 1:
                    echo 'Irasykite zodi: ';
                    $inputLine=$this->inputLoader->getUserInput();
                    $this->timeTracker->startTrackingTime();

                    echo 'Suskiemenuotas zodis: '.$this->hyphenator->hyphenateWord($inputLine,$syllables);
                    $this->timeTracker->endTrackingTime();

                    echo 'Trukme: '.$this->timeTracker->getElapsedTime() . "s\n\n";
                    break;
                case 2:
                    $run = false;
                    break;
            }

        }
    }
}