<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilter()
    {
        $testCase = new TestCase('ClassName','method');
        $this->assertSame('^ClassName\:\:method$', $testCase->getFilter());
    }

    public function testGetFilterWithNamespace()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod');
        $this->assertSame('^TestNamespace\\\\TestCaseClass\:\:testMethod$', $testCase->getFilter());
    }

    public function testGetFilterWithIndexedData()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod', 0);
        $this->assertSame('^TestNamespace\\\\TestCaseClass\:\:testMethod#0$', $testCase->getFilter());
    }

    public function testGetFilterWithNamedData()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod', 'my named data');
        $this->assertSame('^TestNamespace\\\\TestCaseClass\:\:testMethod@my named data$', $testCase->getFilter());
    }
}
 