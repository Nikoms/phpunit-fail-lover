<?php

namespace Nikoms\FailLover\Listener;

use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;

class FailLoverListener extends \PHPUnit_Framework_BaseTestListener
{
    private $mustLogError = false;
    private $filePath;

    function __construct($filePath = '')
    {
        $this->filePath = $filePath;
    }


    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {

        $actions = array();
        $commandLineAttributes = $_SERVER['argv'];
        $positionOfPhpIniOptions = array_keys($commandLineAttributes, '-d');
        $total = count($positionOfPhpIniOptions);
        for ($i = 0; $i < $total; $i++) {
            $option = $commandLineAttributes[$positionOfPhpIniOptions[$i]+1];
            list($key, $value) = explode('=', $option);
            if($key === 'fail-lover'){
                $actions[$value] = true;
            }
        }

        $this->mustLogError = isset($actions['log']);
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if($this->mustLogError && $test instanceof \PHPUnit_Framework_TestCase){
            $this->log($test, $e, $time);
        }
    }

    private function log(\PHPUnit_Framework_TestCase $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
        $reflectionClass = new \ReflectionClass($test);
        echo $reflectionClass->getName().'::'.$test->getName();
        var_export(\PHPUnit_Util_Test::getProvidedData($reflectionClass->getName(), $test->getName(false)));
        echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

    }


}