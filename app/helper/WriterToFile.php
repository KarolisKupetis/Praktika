<?php
namespace App\Helper;

class WriterToFile
{
    public function writeStringToFile($string)
    {
        $file = new \SplFileObject('result.txt', 'w');
        $file->fwrite($string);
    }
}