<?php


namespace Nikoms\FailLover\Listener;


use Nikoms\FailLover\TestCaseResult\ReaderInterface;
use Nikoms\FailLover\TestCaseResult\TestCaseFactory;
use Nikoms\FailLover\Tests\FilterTestMock;

class ReplayListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param array $methodsOfSuite
     * @param array $methodsToFilter
     * @param int $count
     */
    private function assertTestsCountAfterFilter($methodsOfSuite, $methodsToFilter, $count)
    {
        $suite = $this->getSuite($methodsOfSuite);
        $listener = new ReplayListener($this->getReader($methodsToFilter));
        $listener->startTestSuite($suite);
        $this->assertCount($count, $suite);
    }

    /**
     * @param array $methodsToFilter
     * @return \PHPUnit_Framework_MockObject_MockObject|ReaderInterface
     */
    private function getReader($methodsToFilter)
    {
        $testCaseFactory = new TestCaseFactory();
        $testCasesToFilter = array();
        foreach ($methodsToFilter as $testName) {
            $testCasesToFilter[] = $testCaseFactory->createTestCase(new FilterTestMock($testName));
        }

        $reader = $this->getMock('Nikoms\FailLover\TestCaseResult\ReaderInterface', array('getList','isEmpty'));
        $reader->expects($this->any())->method('getList')->will($this->returnValue($testCasesToFilter));
        $reader->expects($this->any())->method('isEmpty')->will($this->returnValue(empty($testCasesToFilter)));
        return $reader;
    }

    /**
     * @param array $methodsToTest
     * @return \PHPUnit_Framework_TestSuite
     */
    private function getSuite($methodsToTest)
    {
        $testCases = array(new FilterTestMock($methodsToTest[0]), new FilterTestMock($methodsToTest[1]));
        $suite = new \PHPUnit_Framework_TestSuite('MySuite');
        $suite->addTest($testCases[0]);
        $suite->addTest($testCases[1]);
        return $suite;
    }

    public function testStartTestSuite_WhenTheFilterListIsEmpty_NoTestWillRun()
    {
        $this->assertTestsCountAfterFilter(array('testToRun', 'testToNotRun'), array(), 0);
    }

    public function testStartTestSuite_WhenAFilterIsInTheTestsList_OnlyOneTestWillRun()
    {
        $this->assertTestsCountAfterFilter(array('testToRun', 'testToNotRun'), array('testToRun'), 1);
    }

    public function testStartTestSuite_WhenAllFiltersAreInTheTestsList_AllTestsWillRun()
    {
        $this->assertTestsCountAfterFilter(array('testToRun', 'testAnotherOne'), array('testToRun', 'testAnotherOne'), 2);
    }

    public function testStartTestSuite_WhenNoneOfTheFilterAreInTheList_NoTestWillRun()
    {
        $this->assertTestsCountAfterFilter(array('testToRun', 'testAnotherOne'), array('testUndefinedTest', 'testUndefined2Test'), 0);
    }

} 