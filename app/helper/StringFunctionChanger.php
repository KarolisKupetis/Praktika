<?php

namespace App\helper;

class StringFunctionChanger
{
    public function pregReplace($replace, $givenString)
    {
        $pattern = '/' . $replace . '*/';
        return preg_replace($pattern, $replace, $givenString);
    }
}