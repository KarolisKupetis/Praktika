<?php
namespace Reader;

class inputLoader
{
    public function getUserInput()
    {
        return trim(fgets(STDIN));
    }
}