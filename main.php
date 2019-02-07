<?php
foreach (glob('src/*.php') as $filename)
{
    include $filename;
}
    define('darboPradziosLaikas', date('h:i:s'));

    $inputLoader = new \first\inputLoader();
    $reader = new \first\fileReader();
    $hyp = new first\Hyphenator();
    $timeTracker = new \first\timeTracker();

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