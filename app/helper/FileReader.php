<?php

namespace App\Helper;

class FileReader
{
    public function readFile($fileName)
    {
        $rows = array();
        $file = new \SplFileObject($fileName);
        $file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        foreach ($file as $line)
        {
            $rows[] = $line;
        }

        return $rows;
    }
}