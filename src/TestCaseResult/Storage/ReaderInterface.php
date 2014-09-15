<?php


namespace Nikoms\FailLover\TestCaseResult\Storage;


use Nikoms\FailLover\TestCaseResult\TestCase;

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
