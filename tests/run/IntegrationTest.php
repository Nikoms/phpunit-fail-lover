<?php


namespace Nikoms\FailLover;

use Nikoms\FailLover\Listener\ReplayListener;
use Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface;
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
        $_SERVER['argv'] = array('-d', 'fail-lover=replay');
        $this->reader = $this->getMockBuilder('Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface')
            ->setMethods(array('getList', 'isEmpty'))
            ->getMock();
    }

    public function tearDown()
    {
        $_SERVER['argv'] = array();
    }

    /**
     * @return \PHPUnit_Framework_TestSuite
     */
    private function createSuiteWithTests()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Test');
        foreach (func_get_args() as $test) {
            if (is_array($test)) {
                $mock = new FilterTestMock(key($test), array('faked data'), current($test));
            } else {
                $mock = new FilterTestMock($test);
            }
            $suite->addTest($mock);
        }
        return $suite;
    }

    private function prepareReaderWithTests()
    {
        $tests = array();
        foreach (func_get_args() as $testToRun) {
            $dataName = null;
            if (is_array($testToRun)) {
                $method = key($testToRun);
                $dataName = $testToRun[$method];
            } else {
                $method = (string)$testToRun;
            }

            $tests[] = new TestCase('Nikoms\FailLover\Tests\FilterTestMock', $method, $dataName);
        }

        $this->reader->expects($this->any())->method('getList')->will($this->returnValue($tests));
        $this->reader->expects($this->any())->method('isEmpty')->will($this->returnValue(empty($tests)));
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @param array $expectedTestsNames
     */
    private function assertSuiteExecutesTests(\PHPUnit_Framework_TestSuite $suite, array $expectedTestsNames)
    {
        $this->addFilterOnSuite($suite);
        $this->assertCount(count($expectedTestsNames), $suite);
        $i = 0;
        foreach ($suite as $test) {
            $this->assertSame($expectedTestsNames[$i], $test->getName(true));
            $i++;
        }
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    private function addFilterOnSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $replayListener = new ReplayListener($this->reader);
        $replayListener->startTestSuite($suite);
    }

    public function testFilter_WhenASingleFilterIsSet_OnlyOneTestIsRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->prepareReaderWithTests('testSimple');
        $this->assertSuiteExecutesTests($suite, array('testSimple'));
    }

    public function testFilter_WhenTwoFiltersAreSet_TwoTestsAreRunning()
    {
        $suite = $this->createSuiteWithTests('testSimple', 'testToRun', 'testThatWontBeExecuted');
        $this->prepareReaderWithTests('testSimple', 'testToRun');
        $this->assertSuiteExecutesTests($suite, array('testSimple', 'testToRun'));
    }

    public function testFilter_WhenAFilterContainsTheNameOfAnother_TheLongTestIsNotTaken()
    {
        $suite = $this->createSuiteWithTests('testContains', 'testContainsFull');
        $this->prepareReaderWithTests('testContains');
        $this->assertSuiteExecutesTests($suite, array('testContains'));
    }

    public function testFilter_WhenAFilterHasIndexedData_OnlyTheSpecifiedIndexedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testWithData' => 0),
            array('testWithData' => 1)
        );

        $this->prepareReaderWithTests(array("testWithData" => 0));

        $this->assertSuiteExecutesTests($suite, array('testWithData with data set #0'));
    }

    public function testFilter_WhenAFilterHasNamedData_OnlyTheSpecifiedNamedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testWithData' => 'runMe'),
            array('testWithData' => 'forgetMe')
        );

        $this->prepareReaderWithTests(array("testWithData" => 'runMe'));

        $this->assertSuiteExecutesTests($suite, array('testWithData with data set "runMe"'));
    }

    public function testFilter_WhenAFilterHasDoubleQuoteNamedData_OnlyTheSpecifiedNamedTestIsRunning()
    {
        $suite = $this->createSuiteWithTests(
            array('testWithData' => '"runMe"'),
            array('testWithData' => '"forgetMe"')
        );

        $this->prepareReaderWithTests(array("testWithData" => '"runMe"'));

        $this->assertSuiteExecutesTests($suite, array('testWithData with data set ""runMe""'));
    }

} 