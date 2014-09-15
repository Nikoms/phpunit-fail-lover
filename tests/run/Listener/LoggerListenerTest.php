<?php


namespace Nikoms\FailLover\Listener;

use Nikoms\FailLover\Tests\FilterTestMock;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $arguments
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $addInvocation
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $clearInvocation
     * @return LoggerListener
     */
    private function getListener($arguments, \PHPUnit_Framework_MockObject_Matcher_Invocation $addInvocation,  \PHPUnit_Framework_MockObject_Matcher_Invocation $clearInvocation)
    {
        $_SERVER['argv'] = explode(' ', $arguments);
        $recorder = $this->getMock('Nikoms\FailLover\TestCaseResult\Storage\RecorderInterface', array('add','clear','remove'));
        $recorder->expects($addInvocation)->method('add');
        $recorder->expects($clearInvocation)->method('clear');
        $recorder->expects($this->any())->method('remove');
        $listener = new LoggerListener($recorder);
        return $listener;
    }

    /**
     * @param string $arguments
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     */
    private function assertRecorderInvocationWithParametersOnFailure(
        $arguments,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
    ) {
        $listener = $this->getListener($arguments, $invocation, $this->never());
        $listener->addFailure(new FilterTestMock('testSimple'), new \PHPUnit_Framework_AssertionFailedError(), 0);
    }

    /**
     * @param string $arguments
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     */
    private function assertRecorderInvocationWithParametersOnError(
        $arguments,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
    ) {
        $listener = $this->getListener($arguments, $invocation, $this->never());
        $listener->addError(new FilterTestMock('testSimple'), new \Exception(), 0);
    }

    public function testFailingTests_WhenLogIsActive_RecorderIsCalled()
    {
        $this->assertRecorderInvocationWithParametersOnFailure('-d fail-lover=log', $this->once());
        $this->assertRecorderInvocationWithParametersOnError('-d fail-lover=log', $this->once());
    }

    public function testFailingTests_WhenLogIsNotActive_RecorderIsNotCalled()
    {
        $this->assertRecorderInvocationWithParametersOnFailure('', $this->never());
        $this->assertRecorderInvocationWithParametersOnError('', $this->never());
    }

    public function testStartTestSuite_WhenLogIsOn_ClearIsCalled()
    {
        $listener = $this->getListener('-d fail-lover=log', $this->never(), $this->once());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
    }

    public function testStartTestSuite_WhenLogIsOff_ClearIsNotCalled()
    {
        $listener = $this->getListener('', $this->never(), $this->never());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
    }

    public function testStartTestSuite_WhenCallTwice_ClearIsCalledOnlyOnce()
    {
        $listener = $this->getListener('-d fail-lover=log', $this->never(), $this->once());
        $suite = new \PHPUnit_Framework_TestSuite('MyTestSuite');
        $listener->startTestSuite($suite);
        $listener->startTestSuite($suite);
    }

    public function tearDown()
    {
        $_SERVER['argv'] = array();
    }
}
 