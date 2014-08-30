<?php

namespace Nikoms\FailLover\Tests;

class FilterTestMock extends \PHPUnit_Framework_TestCase {

    public function testSimple()
    {
        $this->assertTrue(true);
    }

    public function testToRun()
    {
        $this->assertTrue(true);
    }

    public function testThatWontBeExecuted()
    {
        $this->assertTrue(false);
    }
}
 