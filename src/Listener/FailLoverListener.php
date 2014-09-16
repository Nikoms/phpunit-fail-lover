<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 16/09/2014
 * Time: 00:51
 */

namespace Nikoms\FailLover\Listener;


use Exception;
use Nikoms\FailLover\Storage\FileSystem\Csv\CsvReader;
use Nikoms\FailLover\Storage\FileSystem\Csv\CsvRecorder;
use Nikoms\FailLover\Storage\FileSystem\FileNamePattern;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;

class FailLoverListener extends \PHPUnit_Framework_BaseTestListener
{
    /**
     * @var LoggerListener
     */
    private $loggerListener;
    /**
     * @var ReplayListener
     */
    private $replayListener;

    public function __construct($filePattern)
    {
        $fileGenerator = new FileNamePattern($filePattern);
        $fileName = $fileGenerator->getGeneratedFileName();
        $this->loggerListener = new LoggerListener(new CsvRecorder($fileName));
        $this->replayListener = new ReplayListener(new CsvReader($fileName));
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->loggerListener->startTestSuite($suite);
        $this->replayListener->startTestSuite($suite);
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->loggerListener->addError($test, $e, $time);
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->loggerListener->addFailure($test, $e, $time);
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->loggerListener->endTestSuite($suite);
    }


}
