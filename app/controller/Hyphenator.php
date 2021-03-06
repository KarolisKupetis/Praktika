<?php

namespace App\Controller;

use Monolog\Logger;

class Hyphenator
{
    private $usedPatterns;

    private function findSyllablePositionInWord($input, $syllable, $offset = null)
    {
        $onlyLettersSyllable = preg_replace('/\d/', '', $syllable);
        $foundPosition = strpos(strtolower($input), $onlyLettersSyllable, $offset);

        if ($foundPosition === false) {

            return false;
        }

        return $foundPosition;
    }

    public function hyphenateWord($input, $patternTree)
    {
        $inputWithAddedDots = '.' . $input . '.';
        $inputAsArray = str_split(implode(' ', str_split($inputWithAddedDots)));
        $this->usedPatterns= array();
        $patternsArray=$this->getMatchingPatterns($patternTree, $inputWithAddedDots);

        foreach ($patternsArray as $syllable) {

            $syllablePosition = $this->findSyllablePositionInWord($inputWithAddedDots, $syllable);

            while ($syllablePosition !== false) {
                $this->usedPatterns[]=$syllable;
                $spaceIndexInWord = $syllablePosition * 2 + 1;
                $inputAsArray = $this->updateArrayNumbers($spaceIndexInWord, $inputAsArray, $syllable);
                $syllablePosition = $this->findSyllablePositionInWord($inputWithAddedDots, $syllable, $syllablePosition + 1);
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

    public function getUsedPatterns()
    {
        return $this->usedPatterns;
    }

    private function getMatchingPatterns($patternTree, $inputWord)
    {
        $matches = array();
        $wordLength = strlen($inputWord);

        for ($i = 0; $i < $wordLength - 2; $i++) {
            if (isset($patternTree[$inputWord[$i]][$inputWord[$i + 1]][$inputWord[$i + 2]])) {

                foreach ($patternTree[$inputWord[$i]][$inputWord[$i + 1]][$inputWord[$i + 2]] as $item) {
                    $matches[] =$item;
                }
            }

            if (isset($patternTree[$inputWord[$i]][$inputWord[$i + 1]][0])){

                foreach ($patternTree[$inputWord[$i]][$inputWord[$i + 1]][0] as $item) {
                    $matches[] =$item;
                }
            }
        }

        return $matches;
    }
}