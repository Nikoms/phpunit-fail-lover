<?php


namespace Nikoms\FailLover\TestCaseResult;


interface ReaderInterface
{
    /**
     * @return TestCase[]
     */
    public function getList();

    /**
     * @return bool
     */
    public function isEmpty();
}
