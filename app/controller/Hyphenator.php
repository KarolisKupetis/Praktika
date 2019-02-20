<?php

namespace App\Controller;

use Monolog\Logger;

class Hyphenator
{

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger=$logger;
    }

    private function findSyllablePositionInWord($input, $syllable, $offset = null)
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
        $inputWithDots = '.' . $input . '.';
        $inputAsArray = str_split(implode(' ', str_split($inputWithDots)));

        foreach ($patternsArray as $syllable) {

            $syllablePosition = $this->findSyllablePositionInWord($inputWithDots, $syllable);

            while ($syllablePosition !== false) {

                $spaceIndexInWord = $syllablePosition * 2 + 1;
                $inputAsArray = $this->updateArrayNumbers($spaceIndexInWord, $inputAsArray, $syllable);
                $syllablePosition = $this->findSyllablePositionInWord($inputWithDots, $syllable, $syllablePosition + 1);
            }
        }

        $hyphenedWord = $this->arrayToHyphenatedWord($inputAsArray);

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

            if (!$isNotFirstSpace && is_numeric($syllable[$syllableIndex])) {
                $spaceIndexInWord -= 2;
            }

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