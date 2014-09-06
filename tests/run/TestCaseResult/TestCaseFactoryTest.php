<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\Tests\FilterTestMock;

class TestCaseFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testCreateTestCase_WhenTheTestDoesNotHaveDataName_DataNameIsNull()
    {
        $factory = new TestCaseFactory();
        $testCase = $factory->createTestCase(new FilterTestMock('testSimple'));
        $this->assertNull($testCase->getDataName());
    }
}
 