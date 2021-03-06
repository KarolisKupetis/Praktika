<?php

namespace App\Helper;

class TimeTracker implements TimeTrackInterface
{
    private $startTime;
    private $endTime;
    private $timePassed = 0;

    public function startTrackingTime()
    {
        $this->startTime = microtime(true);
    }

    public function endTrackingTime()
    {
        $this->endTime = microtime(true);
    }

    public function getElapsedTime()
    {
        return number_format($this->timePassed = ($this->endTime - $this->startTime), 6);
    }

    public static function currentTime()
    {
        echo date('h:i:s');
    }
}