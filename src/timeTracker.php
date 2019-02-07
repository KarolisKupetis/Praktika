<?php
namespace first;

class timeTracker
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
}