<?php
namespace first;

class inputLoader
{
    public function getUserInput()
    {
        return trim(fgets(STDIN));
    }
}