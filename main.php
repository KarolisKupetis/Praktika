<?php
require 'vendor/autoload.php';

$control = new \App\Controller\HyphenateController();

$control->beginWork();

//file_put_content() & serialize()/deserialize() are your friends... You can just push entire class heirachies to the hard disk.﻿
//preg_match_all('/[A-Za-z@#]+|\S/', $line, $matches );
//print_r($matches);
//$matches='';
//[^\W#]+
//[^a-zA-Z0-9]
//\w+
//sitas geras [A-Za-z@#]+|\S
//'/([\W])\s*+/u' tik tarpai blogai kaikurie
//pauliaus ([^a-zA-Z])\s*+
//$zodis = hyphenate(zodis);