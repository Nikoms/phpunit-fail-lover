<?php


namespace Nikoms\FailLover\TestCaseResult\Storage;


interface RecorderInterface
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     */
    public function add(\PHPUnit_Framework_TestCase $testCase);

    /**
     * empty the file
     * @return void
     */
    public function clear();

    /**
     * @return bool
     */
    public function remove();
}
