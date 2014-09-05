<?php

namespace Nikoms\FailLover\Listener;

use Exception;
use Nikoms\FailLover\Command\ArgumentParser;
use Nikoms\FailLover\TestCaseResult\RecorderInterface;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;


class FailLoverListener extends \PHPUnit_Framework_BaseTestListener
{

    /**
     * @var ArgumentParser
     */
    private $parser;
    /**
     * @var RecorderInterface
     */
    private $recorder;

    public function __construct(RecorderInterface $recorder)
    {
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
        if ($this->parser->hasAction('log') && $test instanceof \PHPUnit_Framework_TestCase) {
            $this->recorder->add($test);
        }
    }
}
