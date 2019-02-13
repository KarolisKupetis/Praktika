<?php

namespace App\Controller;

use App\helper\LoggerCreator;
use App\Helper\TimeTracker;

class Hyphenator
{

    private $logger;
    private $timeTracker;

    public function __construct()
    {
        $this->logger=LoggerCreator::getInstance();
        $this->timeTracker=new TimeTracker();
    }

    private function isSyllableInString($input, $syllable, $offset = null)
    {
        $onlyLettersSyllable = preg_replace('/\d/', '', $syllable);
        $foundPosition = strpos(strtolower($input), $onlyLettersSyllable, $offset);

        if ($foundPosition === false) {

            return false;
        }

        return $foundPosition;
    }

    public function hyphenateWord($input, $patternsArray)
    {
        $this->timeTracker->startTrackingTime();
        $this->logger->addToMessage('Given Word : { '.$input.' }');
        $dotInput = '.' . $input . '.';
        $inputAsArray = str_split(implode(' ', str_split($dotInput)));
        $iterationAmount=0;
        $this->logger->addToMessage(' Patterns:{');

        foreach ($patternsArray as $syllable) {
            $iterationAmount++;
            $syllablePlace = $this->isSyllableInString($dotInput, $syllable);

            while ($syllablePlace !== false) {
                $this->logger->addToMessage($syllable.' ');
                $spaceIndexInWord = $syllablePlace * 2 + 1;
                $inputAsArray = $this->updateArrayNumbers($spaceIndexInWord, $inputAsArray, $syllable);
                $syllablePlace = $this->isSyllableInString($dotInput, $syllable, $syllablePlace + 1);
            }

        }
        $this->logger->addToMessage('} Iteration amount: '.$iterationAmount.' ');
        $hyphenedWord = $this->arrayToHyphenatedWord($inputAsArray);
        $this->timeTracker->endTrackingTime();
        $elapsedTime = $this->timeTracker->getElapsedTime();
        $this->logger->addToMessage('Hyphened word :{ '.$hyphenedWord.' }'.' Elapsed time: '.$elapsedTime);
        $this->logger->logToFile();
        return $hyphenedWord;
    }

    private function updateArrayNumbers($spaceIndexInWord, Array $inputAsArray, $syllable)
    {
        $syllableSize = strlen($syllable);
        $syllableIndex = 0;

        while ($syllableIndex < $syllableSize) {
            $isElementANumber = is_numeric($syllable[$syllableIndex]);
            $isNotLastSpace = $spaceIndexInWord < count($inputAsArray) - 1;
            $isNotFirstSpace = $spaceIndexInWord > 1 + 2;

            if ($isElementANumber && $isNotLastSpace && $isNotFirstSpace) {
                $spaceIndexInWord -= 2;

                if ($inputAsArray[$spaceIndexInWord] < $syllable[$syllableIndex]) {
                    $inputAsArray[$spaceIndexInWord] = $syllable[$syllableIndex];
                }

            }

            $syllableIndex++;
            $spaceIndexInWord += 2;

        }

        return $inputAsArray;
    }

    private function arrayToHyphenatedWord(Array $array)
    {
        foreach ($array as &$arrayElement) {

            if (is_numeric($arrayElement)) {

                if ($arrayElement % 2 !== 0) {
                    $arrayElement = '-';
                } else {
                    $arrayElement = '';
                }

            } elseif ($arrayElement === ' ' || $arrayElement === '.') {
                $arrayElement = '';
            }
        }
        unset($arrayElement);
        return implode('', $array);
    }




}