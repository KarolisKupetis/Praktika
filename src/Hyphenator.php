<?php
namespace first;

class Hyphenator extends timeTracker
{
    private function  arrayToHyphenatedWord(Array $array)
    {
        foreach ($array as &$arrayElement)
        {
            if (is_numeric($arrayElement))
            {
                if ($arrayElement % 2 !== 0)
                {
                    $arrayElement = '-';
                }
                else
                {
                    $arrayElement = '';
                }
            } elseif ($arrayElement === ' ')
            {
                $arrayElement = '';
            }
        }

        return implode('', $array) . "\n";
    }


    private function isSyllableInString($input, $syllable)
    {
        $onlyLettersSyllable = str_replace('.', '', preg_replace('/\d/', '', $syllable));
        $foundPosition = strpos($input, $onlyLettersSyllable);
        if($foundPosition===false)
        {
            return false;
        }

        return $foundPosition;
    }

    private function typeOfSyllable($foundPosition,$syllable,$input)
    {
        $onlyLettersSyllable = str_replace('.', '', preg_replace('/\d/', '', $syllable));
        $lastSyllableElement = $syllable[strlen($syllable)-1];
        $isWordsLastLetters = $foundPosition === strlen($input) - strlen($onlyLettersSyllable);

        if($foundPosition === 0 && $syllable[0] === '.') {

            return 1; //syllable for beginning of the word
        }
        elseif($lastSyllableElement === '.' && $isWordsLastLetters){

            return 2; //syllable for ending of the word
        }
        elseif($syllable[0] !== '.' && $syllable[strlen($syllable) - 1] !== '.') {

            return 3; //syllable for anywhere
        }

        return 0;
    }

    public function hyphenateWord($input, $patternsArray)
    {
        $inputAsArray = str_split(implode(' ', str_split($input)));

        foreach ($patternsArray as $syllable)
        {
            $syllableSize = strlen($syllable);
            $syllableIndex = 0;
            $syllablePlace = $this->isSyllableInString($input,$syllable);
            $syllableType = 0;

            if($syllablePlace!==false)
            {
                $syllableType = $this->typeOfSyllable($syllablePlace,$syllable,$input);
                $foundSyllableAtWordIndex = $syllablePlace * 2 + 1;
            }

            switch($syllableType){
                case 1:
                    $syllableIndex = 1;
                    $inputAsArray = $this->updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
                    break;
                case 2:
                    --$syllableSize;
                    $inputAsArray = $this->updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
                    break;
                case 3:
                    $inputAsArray = $this->updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
                    break;
            }
        }
        return $this->arrayToHyphenatedWord($inputAsArray);
    }

    private function updateArrayNumbers($syllableIndex, $spaceIndexInWord, Array $inputAsArray, $syllable, $syllableSize)
    {
        while ($syllableIndex < $syllableSize)
        {
            $isElementANumber = is_numeric($syllable[$syllableIndex]);
            $isNotLastSpace = $spaceIndexInWord < count($inputAsArray) + 1;
            $isNotFirstSpace = $spaceIndexInWord > 1;
            if ($isElementANumber && $isNotLastSpace && $isNotFirstSpace)
            {
                $spaceIndexInWord -= 2;
                if ($inputAsArray[$spaceIndexInWord] < $syllable[$syllableIndex])
                {
                    $inputAsArray[$spaceIndexInWord] = $syllable[$syllableIndex];
                }
            }
            $syllableIndex++;
            $spaceIndexInWord += 2;
        }

        return $inputAsArray;
    }
}