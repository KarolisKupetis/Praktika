<?php


namespace first;


class inputLoader
{
    public static function getUserInput()
    {
        return trim(readline(STDIN));
    }
}