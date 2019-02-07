<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.7
 * Time: 17.26
 */

namespace first;


interface TimeTrackInterface
{
     public function startTrackingTime();
     public function endTrackingTime();
     public function stopTrack();
}