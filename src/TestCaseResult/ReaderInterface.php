<?php


namespace Nikoms\FailLover\TestCaseResult;


interface ReaderInterface
{
    /**
     * @return TestCase[]
     */
    public function getList();
}
