<?php

namespace App\Helper;

interface TimeTrackInterface
{
     public function startTrackingTime();
     public function endTrackingTime();
     public function getElapsedTime();
}