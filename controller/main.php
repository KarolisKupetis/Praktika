<?php
require '../vendor/autoload.php';

    define('BEGINTIME', date('h:i:s'));

    $inputLoader = new Reader\inputLoader();

    $reader = new Reader\fileReader();
    $hyp = new Logic\Hyphenator\Hyphenator();
    $timeTracker = new Helper\timeTracker();

    $syllables = $reader->readFile('tekstas');

    $run = true;

    while($run)
    {
        echo "1. Ivesti zodi ir ji suskiemenuoti\n";
        echo "2. Baigti darba\n";
        $inputLine = $inputLoader->getUserInput();

        switch ($inputLine) {
            case 1:
                $inputLine = $inputLoader->getUserInput();
                $timeTracker->startTrackingTime();
                echo $hyp->hyphenateWord($inputLine, $syllables);
                $timeTracker->endTrackingTime();
                echo $timeTracker->getElapsedTime()."\n";
                break;
            case 2:
                $run = false;
                break;
        }
    }