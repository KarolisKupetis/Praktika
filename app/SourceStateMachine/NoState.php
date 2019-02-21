<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.20
 * Time: 12.39
 */

namespace App\SourceStateMachine;


class NoState implements StateInterface
{

    public function hyphenateWord($inputWord)
    {
        echo "No source selected. First, select a source \n";

        return null;
    }

    public function getPatterns()
    {
        echo "No source selected. First, select a source \n";

        return null;
    }
}