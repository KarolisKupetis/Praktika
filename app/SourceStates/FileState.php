<?php

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\database\DatabaseController;
use App\Helper\FileReader;
use App\Helper\InputLoader;
use Psr\Log\LoggerInterface;

class FileState implements StateInterface
{
    private $fileReader;
    private $patterns;
    private $hyphenator;
    private $logger;

    public function __construct($filename,LoggerInterface $logger)
    {
        $this->logger=$logger;
        $this->fileReader=new FileReader();
        $this->hyphenator= new Hyphenator();
        $this->patterns= $this->setPatterns($filename);
    }

    public function hyphenateWord($inputWord)
    {

        return $this->hyphenator->hyphenateWord($inputWord,$this->patterns);
    }

    private function setPatterns($fileName)
    {
         return $this->fileReader->readFile($fileName);

    }

    public function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key => $element) {

            if ($this->isWord($element) === true) {
                $hyphenedSentence[$key] = $this->hyphenateOneWordFromSentence($element);
            }
        }

        $result = implode($hyphenedSentence) . "\n";
        $this->logger->info('Hyphened sentence' . $result);

        return $result;
    }

    public function hyphenateFile($fileName)
    {
        $fileContent = $this->fileReader->readFile($fileName);
        $result = '';

        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result .= $hyphenedSentence;
        }

        return $result;
    }

    private function isWord($subject)
    {
        if (preg_match('/[^a-zA-Z]/', $subject)) {

            return false;
        }

        return true;
    }

    private function hyphenateOneWordFromSentence($word)
    {
        return $this->hyphenator->hyphenateWord($word, $this->patterns);
    }

}