<?php
namespace App\Helper;

class WriterToFile
{
    public function writeStringToFile($string)
    {
        $file = new \SplFileObject('resultNotTree.txt', 'a+');
        $file->fwrite($string);
    }
}