<?php
require 'vendor/autoload.php';

//$control = new \App\Controller\HyphenateController();

//$control->beginWork();
$db = new \App\database\Controller();
$array = array('words','big');
$dbs = new \App\database\Controller();
$db->saveWordHyphenation($array,'oneword','one-word');




