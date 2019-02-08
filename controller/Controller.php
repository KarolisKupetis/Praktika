<?php
//
//namespace Controller;
//
//class Controller
//{
//    private $inputLoader;
//    private $reader;
//    private $hyp;
//    private $timeTracker;
//    private $run = false;
//
//    function beginWork()
//    {
//        $inputLoader = new Reader\inputLoader();
//
//        $reader = new Reader\fileReader();
//        $hyp = new Logic\Hyphenator\Hyphenator();
//        $timeTracker = new Helper\timeTracker();
//
//        $run = true;
//
//        while ($run) {
//            echo "1. Ivesti zodi ir ji suskiemenuoti\n";
//            echo "2. Baigti darba\n";
//            $this->setInputLoader();
//            $inputLine=$this->getInputLoader();
//
//            switch ($inputLine) {
//                case 1:
//                    $inputLine=$this->getInputLoader();
//                    $this->timeTracker =  new Helper\timeTracker();
//
//                    $timeTracker = $this->getTimeTracker();
//
//                    echo $hyp->hyphenateWord($inputLine, $syllables);
//                    $timeTracker->endTrackingTime();
//                    echo $timeTracker->getElapsedTime() . "\n";
//                    break;
//                case 2:
//                    $run = false;
//                    break;
//            }
//        }
//
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getInputLoader()
//    {
//        return $this->inputLoader;
//    }
//
//    /**
//     * @param mixed $inputLoader
//     */
//    public function setInputLoader($inputLoader)
//    {
//        $this->inputLoader = $inputLoader;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getReader()
//    {
//        return $this->reader;
//    }
//
//    public function setReader()
//    {
//        $this->reader = new Reader\fileReader();
//    }
//
//
//    public function getHyp()
//    {
//        return $this->hyp;
//    }
//
//
//    public function setHyp()
//    {
//        $this->hyp = new Logic\Hyphenator\Hyphenator();
//    }
//
//
//    public function getTimeTracker()
//    {
//        return $this->timeTracker;
//    }
//
//    public function setTimeTracker()
//    {
//        $this->timeTracker = new Helper\timeTracker();
//    }
//
//    public function isRun()
//    {
//        return $this->run;
//    }
//
//    public function setRun($run)
//    {
//        $this->run = $run;
//    }
//
//}