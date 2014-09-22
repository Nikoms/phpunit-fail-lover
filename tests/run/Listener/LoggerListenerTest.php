<?php


namespace Nikoms\FailLover\Listener;

use Nikoms\FailLover\Tests\FilterTestMock;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $arguments
     */
    private function initCommandArguments($arguments)
    {
        $_SERVER['argv'] = explode(' ', $arguments);
    }

    public function tearDown()
    {
        $_SERVER['argv'] = array();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $addInvocation
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $clearInvocation
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $removeInvocation
     * @return LoggerListener
     */
    private function getListener(
        \PHPUnit_Framework_MockObject_Matcher_Invocation $addInvocation,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $clearInvocation,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $removeInvocation
    ) {
        $recorder = $this->getMock(
            'Nikoms\FailLover\TestCaseResult\Storage\RecorderInterface',
            array('add', 'clear', 'remove')
        );
        $recorder->expects($addInvocation)->method('add');
        $recorder->expects($clearInvocation)->method('clear');
        $recorder->expects($removeInvocation)->method('remove');
        $listener = new LoggerListener($recorder);

        return $listener;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     */
    private function assertRecorderInvocationOnFailure(
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
    ) {
        $listener = $this->getListener($invocation, $this->never(), $this->never());
        $listener->addFailure(new FilterTestMock('testSimple'), new \PHPUnit_Framework_AssertionFailedError(), 0);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     */
    private function assertRecorderInvocationOnError(
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
    ) {
        $listener = $this->getListener($invocation, $this->never(), $this->never());
        $listener->addError(new FilterTestMock('testSimple'), new \Exception(), 0);
    }

    public function testFailingTests_WhenLogIsActive_RecorderIsCalled()
    {
        $this->assertRecorderInvocationOnFailure($this->once());
        $this->assertRecorderInvocationOnError($this->once());
    }

    public function testFailingTests_WhenLogIsNotDisabled_RecorderIsNotCalled()
    {
        $this->disableLog();
        $this->assertRecorderInvocationOnFailure($this->never());
        $this->assertRecorderInvocationOnError($this->never());
    }

    public function testStartTestSuite_WhenLogIsOn_ClearIsCalled()
    {
        $listener = $this->getListener($this->never(), $this->once(), $this->never());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
    }

    public function testStartTestSuite_WhenLogIsNotDisabled_ClearIsNotCalled()
    {
        $this->disableLog();
        $listener = $this->getListener($this->never(), $this->never(), $this->never());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
    }

    public function testStartTestSuite_WhenCallTwice_ClearIsCalledOnlyOnce()
    {
        $listener = $this->getListener($this->never(), $this->once(), $this->never());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
        $listener->startTestSuite($suite);
    }

    public function testEndTestSuite_WhenNoTestWasRecorded_ThenCallRemove()
    {
        $listener = $this->getListener($this->never(), $this->never(), $this->once());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->endTestSuite($suite);
    }

    public function testEndTestSuite_WhenOneTestWasRecorded_ThenNeverCallRemove()
    {
        $listener = $this->getListener($this->any(), $this->never(), $this->never());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->addError(new FilterTestMock('testMe'), new \Exception(), 0);
        $listener->endTestSuite($suite);
    }

    private function disableLog()
    {
        $this->initCommandArguments('-d fail-lover=log:disabled');
    }
}
 