<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $testCase = new TestCase('ClassName','method');
        $this->assertSame('ClassName::method', $testCase->getName());
    }

    public function testGetNameWithNamespace()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod');
        $this->assertSame('TestNamespace\TestCaseClass::testMethod', $testCase->getName());
    }

    public function testGetNameWithIndexedData()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod', 0);
        $this->assertSame('TestNamespace\TestCaseClass::testMethod#0', $testCase->getName());
    }

    public function testGetNameWithNamedData()
    {
        $testCase = new TestCase('TestNamespace\TestCaseClass','testMethod', 'my named data');
        $this->assertSame('TestNamespace\TestCaseClass::testMethod@my named data', $testCase->getName());
    }

}
 