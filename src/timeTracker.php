<?php
namespace first;


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

    public function stopTrack()
    {
        $this->timePassed =($this->startTime - $this->endTime) / 60;
    }
    public function getElapsedTime()
    {
        return $this->timePassed;
    }
}