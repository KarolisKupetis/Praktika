<?php

namespace Helper;

class timeTracker implements TimeTrackInterface
{
    private $startTime;
    private $endTime;
    private $timePassed=0;

    public function startTrackingTime()
    {
       $this->startTime= microtime(true);
    }

    public function endTrackingTime()
    {
        $this->endTime = microtime(true);
    }

    public function getElapsedTime()
    {
        return $this->timePassed =($this->endTime-$this->startTime) / 60;
    }

    public static function currentTime()
    {
        echo date('h:i:s');
    }
}