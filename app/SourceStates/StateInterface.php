<?php

namespace App\SourceStateMachine;

interface StateInterface
{
    public function hyphenateWord($inputWord);
    public function hyphenateSentence($sentence);
    public function hyphenateFile($fileName);
}