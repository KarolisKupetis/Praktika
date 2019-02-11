<?php
require ('vendor/autoload.php');

define('BEGINTIME', date('h:i:s'));

$control = new \App\Controller\Controller();
$control->beginWork();
