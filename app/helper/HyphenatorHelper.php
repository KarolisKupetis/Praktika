<?php


namespace App\helper;


class HyphenatorHelper
{

    private function hyphenateFromFile($filename)
    {
        $this->timeTracker->startTrackingTime();
        $result = "\n" . $this->hyphenateFile($filename);
        $this->timeTracker->endTrackingTime();
        echo $result;
        $elapsedTime = $this->timeTracker->getElapsedTime();
        echo "\n" . 'Time taken : ' . $elapsedTime . "\n";
    }

    private function hyphenateWordFromCMD()
    {
        echo 'Type in the word you want to hyphenate: ';
        $inputLine = $inputLine = $this->inputLoader->getUserInput();

        $this->timeTracker->startTrackingTime();
        $result = $this->hyphenateOneWord($inputLine);
        $this->timeTracker->endTrackingTime();
        echo 'Hyphenated word: ' . $result . "\n";
        echo 'Time taken: ' . $this->timeTracker->getElapsedTime() . "\n";
    }

    private function hyphenateSentence($sentence)
    {
        $sentenceAsArray = preg_split('/([^a-zA-Z])/u', $sentence, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $hyphenedSentence = $sentenceAsArray;

        foreach ($sentenceAsArray as $key => $element) {

            if ($this->isWord($element) === true) {
                $hyphenedSentence[$key] = $this->hyphenateOneWord($element);
            }
        }

        return implode($hyphenedSentence) . "\n";
    }

    private function hyphenateFile($fileName)
    {
        $fileContent = $this->fileReader->readFile($fileName);
        $result ='';
        foreach ($fileContent as $sentence) {
            $hyphenedSentence = $this->hyphenateSentence($sentence);
            $result.=$hyphenedSentence;
        }
        return $result;
    }
}