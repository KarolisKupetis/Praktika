<?php

interface TimeTrackInterfaces
{
     public function startTrackingTime();
     public function endTrackingTime();
     public function getElapsedTime();
}