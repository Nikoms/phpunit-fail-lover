<?php


namespace Nikoms\FailLover;


use Nikoms\FailLover\Filter\Filter;
use Nikoms\FailLover\Filter\FilterFactory;
use Nikoms\FailLover\TestCaseResult\ReaderInterface;
use Nikoms\FailLover\TestCaseResult\TestCase;
use Nikoms\FailLover\Tests\FilterTestMock;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{


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


    /**
     * @return \PHPUnit_Framework_TestSuite
     */
    private function createSuiteWithTests()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Test');
        foreach (func_get_args() as $test) {
            if (is_array($test)) {
                $mock = new FilterTestMock($test['testName'], array('faked data'), $test['dataName']);
            } else {
                $mock = new FilterTestMock($test);
            }
            $suite->addTest($mock);
        }
        return $suite;
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @param array $tests
     */
    private function assertSuiteExecuteTests(\PHPUnit_Framework_TestSuite $suite, array $tests)
    {
        $this->addFilterOnSuite($suite);
        $this->assertCount(count($tests), $suite);
        $i = 0;
        foreach ($suite as $test) {
            $this->assertSame($tests[$i], $test->getName(true));
            $i++;
        }
    }

    public function testFilter_WhenASingleFilterIsSet_OnlyOneTestIsRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple')
            )
        );

        $this->addFilterOnSuite($suite);
        $this->assertCount(1, $suite);
        foreach ($suite as $test) {
            $this->assertSame('testSimple', $test->getName(true));
        }
    }

    public function testFilter_WhenTwoFiltersAreSet_TwoTestsAreRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple'),
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testToRun')
            )
        );

        $this->assertSuiteExecuteTests($suite, array('testSimple', 'testToRun'));
    }

    public function testFilter_WhenAFilterContainsTheNameOfAnother_TheLongTestIsNotTaken()
    {
        $suite = $this->createSuiteWithTests('testContains', 'testContainsFull');
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testContains'),
            )
        );

        $this->assertSuiteExecuteTests($suite, array('testContains'));
    }

    public function testFilter_WhenAFilterHasIndexedData_OnlyTheSpecifiedIndexedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testName' => 'testWithData', 'dataName' => 0),
            array('testName' => 'testWithData', 'dataName' => 1)
        );
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testWithData', 0),
            )
        );

        $this->assertSuiteExecuteTests($suite, array('testWithData with data set #0'));
    }

    public function testFilter_WhenAFilterHasNamedData_OnlyTheSpecifiedNamedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testName' => 'testWithData', 'dataName' => 'runMe'),
            array('testName' => 'testWithData', 'dataName' => 'forgetMe')
        );
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testWithData', 'runMe'),
            )
        );

        $this->assertSuiteExecuteTests($suite, array('testWithData with data set "runMe"'));
    }
    public function testFilter_WhenAFilterHasDoubleQuoteNamedData_OnlyTheSpecifiedNamedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testName' => 'testWithData', 'dataName' => '"runMe"'),
            array('testName' => 'testWithData', 'dataName' => '"forgetMe"')
        );
        $this->reader->expects($this->any())->method('getList')->willReturn(
            array(
                new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testWithData', '"runMe"'),
            )
        );

        $this->assertSuiteExecuteTests($suite, array('testWithData with data set ""runMe""'));
    }

} 