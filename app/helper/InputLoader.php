<?php
namespace App\Helper;

class InputLoader
{
    public function getUserInput()
    {
        return trim(fgets(STDIN));
    }
}