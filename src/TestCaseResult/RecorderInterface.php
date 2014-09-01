<?php


namespace Nikoms\FailLover\TestCaseResult;


interface RecorderInterface
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     */
    public function add(\PHPUnit_Framework_TestCase $testCase);
} 