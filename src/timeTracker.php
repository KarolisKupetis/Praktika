<?php
namespace first;


class timeTracker
{
    protected $startTime=0;
    protected $endTime=0;
    protected $timePassed=0;

    public function startTrackingTime()
    {
       $this->startTime= microtime(true);
    }

    public function endTrackingTime()
    {
        $this->endTime = microtime(true);
    }

    public function getPassedTime()
    {
        $this->timePassed =($this->startTime - $this->endTime) / 60;
    }
}