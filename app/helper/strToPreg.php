<?php


namespace App\helper;


class strToPreg
{
    public function pregReplace($replace, $givenString)
    {
        $pattern = '/' . $replace . '*/';
        echo preg_replace($pattern, $replace, $givenString);
    }

    // [A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}
    //for all words w/o numbers /\b[^\d\W]+\b/
}