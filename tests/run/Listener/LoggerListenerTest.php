<?php


namespace Nikoms\FailLover\Listener;

use Nikoms\FailLover\Tests\FilterTestMock;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $arguments
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     * @return LoggerListener
     */
    private function getListener($arguments, \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation)
    {
        $_SERVER['argv'] = explode(' ', $arguments);
        $recorder = $this->getMock('Nikoms\FailLover\TestCaseResult\RecorderInterface', array('add'));
        $recorder->expects($invocation)->method('add');
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
        $listener = $this->getListener($arguments, $invocation);
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
        $listener = $this->getListener($arguments, $invocation);
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

    public function tearDown()
    {
        $_SERVER['argv'] = array();
    }
}
 