<?php

foreach (glob('src/*.php') as $filename)
{
    include $filename;
}

$inputLine = trim(fgets(STDIN));

$reader = new \first\fileReader();
$syllables = $reader->readFile('tekstas');


$hyp = new first\Hyphenator();
echo $hyp->hyphenateWord($inputLine,$syllables);



