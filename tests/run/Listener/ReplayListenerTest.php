<?php


namespace Nikoms\FailLover\Listener;


use Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface;
use Nikoms\FailLover\TestCaseResult\TestCaseFactory;
use Nikoms\FailLover\Tests\FilterTestMock;

class ReplayListenerTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        $_SERVER['argv'] = array();
    }

    /**
     * @param $arguments
     */
    private function initCommandArguments($arguments)
    {
        $_SERVER['argv'] = explode(' ', $arguments);
    }

    private function disableListener()
    {
        $this->initCommandArguments('-d fail-lover=replay:disabled');
    }

    /**
     * @param array $methodsOfSuite
     * @param array $methodsToFilter
     * @param int $count
     */
    private function assertTestsCountAfterFilter($methodsOfSuite, $methodsToFilter, $count)
    {
        $suite = $this->getSuite($methodsOfSuite);
        $listener = new ReplayListener($this->getReader($methodsToFilter, $this->any()));
        $listener->startTestSuite($suite);
        $this->assertCount($count, $suite);
    }

    /**
     * @param array $methodsToFilter
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     * @param bool $isValid
     * @return \PHPUnit_Framework_MockObject_MockObject|ReaderInterface
     */
    private function getReader(
        $methodsToFilter,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation,
        $isValid = true
    ) {
        $testCaseFactory = new TestCaseFactory();
        $testCasesToFilter = array();
        foreach ($methodsToFilter as $testName) {
            $testCasesToFilter[] = $testCaseFactory->createTestCase(new FilterTestMock($testName));
        }

        $reader = $this->getMock(
            'Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface',
            array('getList', 'isEmpty', 'isValid')
        );
        $reader->expects($invocation)->method('getList')->will($this->returnValue($testCasesToFilter));
        $reader->expects($invocation)->method('isEmpty')->will($this->returnValue(empty($testCasesToFilter)));
        $reader->expects($invocation)->method('isValid')->will($this->returnValue($isValid));

        return $reader;
    }

    /**
     * @param array $methodsToTest
     * @return \PHPUnit_Framework_TestSuite
     */
    private function getSuite(array $methodsToTest)
    {
        $suite = new \PHPUnit_Framework_TestSuite('MySuite');
        foreach ($methodsToTest as $methodToTest) {
            $suite->addTest(new FilterTestMock($methodToTest));
        }

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
        $this->assertTestsCountAfterFilter(
            array('testToRun', 'testAnotherOne'),
            array('testToRun', 'testAnotherOne'),
            2
        );
    }

    public function testStartTestSuite_WhenNoneOfTheFilterAreInTheList_NoTestWillRun()
    {
        $this->assertTestsCountAfterFilter(
            array('testToRun', 'testAnotherOne'),
            array('testUndefinedTest', 'testUndefined2Test'),
            0
        );
    }

    public function testStartTestSuite_WhenListenerIsDisabled_TheFilterIsNotCalled()
    {
        $this->disableListener();
        $suite = $this->getSuite(array('testToRun'));
        $listener = new ReplayListener($this->getReader(array(), $this->never()));
        $listener->startTestSuite($suite);
        $this->assertCount(1, $suite);
    }

    public function testStartTestSuite_WhenReaderIsNotValid_TheFilterIsNotCalled()
    {
        $suite = $this->getSuite(array('testToRun', 'testAnotherOne'));
        $listener = new ReplayListener($this->getReader(array(), $this->any(), false));
        $listener->startTestSuite($suite);
        $this->assertCount(2, $suite);
    }
}
