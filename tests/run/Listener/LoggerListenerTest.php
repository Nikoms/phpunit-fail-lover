<?php


namespace Nikoms\FailLover\Listener;

use Nikoms\FailLover\Tests\FilterTestMock;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFailure_WhenLogIsActive_RecorderIsCalled()
    {
        $this->assertRecorderInvocationWithParameters(array('-d', 'fail-lover=log'), $this->once());
    }

    public function testAddFailure_WhenLogIsNotActive_RecorderIsNotCalled()
    {
        $this->assertRecorderInvocationWithParameters(array(), $this->never());
    }

    private function assertRecorderInvocationWithParameters(
        array $arguments,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
    ) {
        $_SERVER['argv'] = $arguments;
        $recorder = $this->getMock('Nikoms\FailLover\TestCaseResult\RecorderInterface', array('add'));
        $recorder->expects($invocation)->method('add');
        $listener = new LoggerListener($recorder);
        $listener->addFailure(new FilterTestMock('testSimple'), new \PHPUnit_Framework_AssertionFailedError(), 0);
    }


}
 