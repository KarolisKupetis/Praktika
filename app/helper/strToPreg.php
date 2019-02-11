<?php


namespace App\helper;


class strToPreg
{
    private $example;
    private $replace;

    public function __construct()
    {
        $example = 'jungle in jungling while Junglers jjungggl';
        $replace = "jungl";
    }



    public function pregReplace($replace, $givenString)
    {
        $pattern = '/' . $replace . '*/';
        echo preg_replace($pattern, $replace, $givenString);
    }

    // [A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}
    //for all words w/o numbers /\b[^\d\W]+\b/
    public function pregFind($needle, $haystack)
    {

    }
}