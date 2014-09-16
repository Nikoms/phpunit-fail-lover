<?php

namespace Nikoms\FailLover\Listener;

use Exception;
use Nikoms\FailLover\Command\ArgumentParser;
use Nikoms\FailLover\TestCaseResult\Storage\RecorderInterface;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;


class LoggerListener extends \PHPUnit_Framework_BaseTestListener
{

    /**
     * @var ArgumentParser
     */
    private $parser;
    /**
     * @var RecorderInterface
     */
    private $recorder;

    private $mainSuiteName;

    private $linesAdded;

    public function __construct(RecorderInterface $recorder)
    {
        $this->linesAdded = 0;
        $this->recorder = $recorder;
        $this->parser = new ArgumentParser($_SERVER['argv']);
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->log($test);
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->log($test);
    }

    /**
     * @param PHPUnit_Framework_Test $test
     */
    private function log(PHPUnit_Framework_Test $test)
    {
        if ($this->isLogActive() && $test instanceof \PHPUnit_Framework_TestCase) {
            $this->recorder->add($test);
            $this->linesAdded++;
        }
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->mainSuiteName === null && $this->isLogActive()) {
            $this->recorder->clear();
            $this->mainSuiteName = $suite->getName();
        }
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->linesAdded === 0) {
            $this->recorder->remove();
        }
    }


    /**
     * @return bool
     */
    private function isLogActive()
    {
        return $this->parser->hasAction('log');
    }
}
