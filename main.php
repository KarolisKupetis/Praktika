<?php

$inputLine = trim(fgets(STDIN));

$execStartTime = microtime(true);

$patterns = stringFromFile('tekstas.txt');

$syllablesAsNumbers = findSyllablesInString($inputLine, $patterns);

$syllableWord = unevenNumbersToDashes($syllablesAsNumbers);

echo $syllableWord . "\n";
$execEndTime = microtime(true);
$execTime = ($execEndTime - $execStartTime) / 60;

echo $execTime;

function findSyllablesInString($input, $patternsArray)
{
    $inputAsArray = str_split(implode(' ', str_split($input)));

    foreach ($patternsArray as $syllable)
    {

        $patternOnlyInLetters = str_replace('.', '', preg_replace('/\d/', '', $syllable));

        $syllableSize = strlen($syllable);
        $foundPosition = strpos($input, $patternOnlyInLetters);
        $isBeginningSyllable = $syllable[0] === '.' && $foundPosition === 0;
        $isSyllableFoundOnlyInEnd = $foundPosition === (strlen($input) - strlen($patternOnlyInLetters));
        $isEndingSyllable = $syllable[strlen($syllable) - 1] === '.' && $isSyllableFoundOnlyInEnd;
        $isSyllableInMiddle = $syllable[0] !== '.' && $syllable[strlen($syllable) - 1] !== '.';

        $foundSyllableAtWordIndex = $foundPosition * 2 + 1;
        $syllableIndex = 0;

        if ($isBeginningSyllable)
        {
            $syllableIndex = 1;
            $inputAsArray = updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
        } elseif ($isEndingSyllable)
        {
            --$syllableSize;
            $inputAsArray = updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
        } elseif ($isSyllableInMiddle && $foundPosition !== false)
        {
            $inputAsArray = updateArrayNumbers($syllableIndex, $foundSyllableAtWordIndex, $inputAsArray, $syllable, $syllableSize);
        }

    }

    return $inputAsArray;
}

function unevenNumbersToDashes(Array $array)
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

function updateArrayNumbers($syllableIndex, $spaceIndexInWord, Array $inputAsArray, $syllable, $syllableSize)
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

function stringFromFile($fileName)
{
    $rows = array();
    $file = new SplFileObject($fileName);
    $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
    foreach ($file as $line)
    {
        $rows[] = $line;
    }

    return $rows;
}
