<?php
require 'vendor/autoload.php';

$control = new \App\Controller\HyphenateController();
$control->beginWork();

//file_put_content() & serialize()/deserialize() are your friends... You can just push entire class heirachies to the hard disk.﻿