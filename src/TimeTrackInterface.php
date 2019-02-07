<?php
namespace first;


interface TimeTrackInterface
{
     public function startTrackingTime();
     public function endTrackingTime();
     public function stopTrack();
}