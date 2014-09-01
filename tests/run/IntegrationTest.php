<?php


namespace Nikoms\FailLover;


use Nikoms\FailLover\Filter\Filter;
use Nikoms\FailLover\Filter\FilterFactory;
use Nikoms\FailLover\TestCaseResult\ReaderInterface;
use Nikoms\FailLover\TestCaseResult\TestCase;
use Nikoms\FailLover\Tests\FilterTestMock;

class IntegrationTest extends \PHPUnit_Framework_TestCase{


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ReaderInterface
     */
    private $reader;

    public function setUp()
    {
        $this->reader = $this->getMockBuilder('Nikoms\FailLover\TestCaseResult\ReaderInterface')
            ->setMethods(array('getList'))
            ->getMock();
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    private function addFilterOnSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $filterFactory = new FilterFactory();
        $suite->injectFilter($filterFactory->createFactory(new Filter($this->reader)));
    }

    private function createSuiteWithTests()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Test');
        foreach (func_get_args() as $testName) {
            $suite->addTest(new FilterTestMock($testName));
        }
        return $suite;
    }

    public function testFilter_WhenASingleFilterIsSet_OnlyOneTestIsRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->reader->expects($this->any())->method('getList')->willReturn(array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple')
            )
        );

        $this->addFilterOnSuite($suite);
        $this->assertCount(1, $suite);
    }

    public function testFilter_WhenTwoFiltersAreSet_TwoTestsAreRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->reader->expects($this->any())->method('getList')->willReturn(array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple'),
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testToRun')
            )
        );

        $this->addFilterOnSuite($suite);
        $this->assertCount(2, $suite);
    }

    public function testFilter_WhenAFilterContainsTheNameOfAnother_TheLongTestIsNotTaken()
    {
        $suite = $this->createSuiteWithTests('testContains', 'testContainsFull');
        $this->reader->expects($this->any())->method('getList')->willReturn(array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testContains'),
            )
        );


        $this->addFilterOnSuite($suite);
        $this->assertCount(1, $suite);
    }

} 