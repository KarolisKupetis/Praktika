<?php
namespace Reader;

class writerToFile
{
    public function writeStringToFile($string)
    {
        $file = new \SplFileObject('result.txt', 'w');
        $file->fwrite($string);
    }
}