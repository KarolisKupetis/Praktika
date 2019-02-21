<?php

namespace App\SourceStateMachine;

interface StateInterface
{
    public function hyphenateWord($inputWord);
    public function getPatterns();
}