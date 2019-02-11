<?php

namespace App\Controller;

class Hyphenator
{
    private function isSyllableInString($input, $syllable,$offset=null)
    {
        $onlyLettersSyllable = preg_replace('/\d/', '', $syllable);
        $foundPosition = strpos($input, $onlyLettersSyllable,$offset);

        if ($foundPosition === false) {
            return false;
        }

        return $foundPosition;
    }

    public function hyphenateWord($input, $patternsArray)
    {
        $dotInput = '.' . $input . '.';
        $inputAsArray = str_split(implode(' ', str_split($dotInput)));

        foreach ($patternsArray as $syllable) {
            $syllablePlace = $this->isSyllableInString($dotInput, $syllable);

            while($syllablePlace){

                if ($syllablePlace !== false) {
                    $spaceIndexInWord = $syllablePlace * 2 + 1;
                    $inputAsArray = $this->updateArrayNumbers($spaceIndexInWord, $inputAsArray, $syllable);
                }

                $syllablePlace = $this->isSyllableInString($dotInput, $syllable,$syllablePlace+1);
            }

        }

        return $this->arrayToHyphenatedWord($inputAsArray);
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

        return implode('', $array) . "\n";
    }
}