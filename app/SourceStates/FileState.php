<?php

namespace App\SourceStateMachine;

use App\Controller\Hyphenator;
use App\Helper\FileReader;
use Psr\Log\LoggerInterface;

class FileState implements StateInterface
{
    private $fileReader;
    private $patterns;
    private $hyphenator;
    private $logger;
    private $patternTree;

    public function __construct($filename,LoggerInterface $logger)
    {
        $this->logger=$logger;
        $this->fileReader=new FileReader();
        $this->hyphenator= new Hyphenator();
        $this->patterns= $this->getPatternsFromFile($filename);
        $this->patternTree=$this->getPatternTree($this->patterns);
    }

    public function hyphenateWord($inputWord)
    {

        return $this->hyphenator->hyphenateWord($inputWord,$this->patternTree);
    }

    private function getPatternsFromFile($fileName)
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
        $this->logger->info('Hyphened sentence:  ' . $result);

        return $result;
    }

    public function hyphenateFile($fileName)
    {
        $fileContent = $this->fileReader->readFile($fileName);
        $result = array();

        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result[] = $hyphenedSentence;
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
        return $this->hyphenator->hyphenateWord($word, $this->patternTree);
    }

    public function getPatternTree($patterns)
    {
        $tree[] = array();
        foreach ($patterns as $pattern) {
            $letterPattern = preg_replace('/\d/', '', $pattern);
            if (isset($letterPattern[2])) {
                $tree[$letterPattern[0]][$letterPattern[1]][$letterPattern[2]][] = $pattern;
            } else {
                $tree[$letterPattern[0]][$letterPattern[1]][0][] = $pattern;
            }
        }
        return $tree;
    }

}