<?php


namespace Nikoms\FailLover;


use Nikoms\FailLover\TestCaseResult\TestCase;
use Nikoms\FailLover\Tests\FilterTestMock;

class FilterTest extends \PHPUnit_Framework_TestCase{


    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @param TestCase[] $testCases
     */
    private function addFilterOnSuite(\PHPUnit_Framework_TestSuite $suite, array $testCases)
    {
        $filterFactory = new \PHPUnit_Runner_Filter_Factory();
        $filters = array();
        foreach ($testCases as $testCase) {
            $filters[] = preg_quote($testCase->getName(), '/');
        }
        $filterFactory->addFilter(
            new \ReflectionClass('PHPUnit_Runner_Filter_Test'),
            implode('|', $filters)
        );

        $suite->injectFilter($filterFactory);
    }

    /**
     * @param array $tests
     * @return \PHPUnit_Framework_TestSuite
     */
    private function makeSuite(array $tests)
    {
        $suite = new \PHPUnit_Framework_TestSuite('Test');
        foreach ($tests as $testName) {
            $suite->addTest(new FilterTestMock($testName));
        }
        return $suite;
    }

    /**
     * @group XXX
     */
    public function testFilter_WhenASingleFilterIsSet_OnlyOneTestIsRunning()
    {
        $suite = $this->makeSuite(array('testSimple', 'testToRun', 'testThatWontBeExecuted'));
        $testToRun = new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple');
        $this->addFilterOnSuite($suite, array($testToRun));
        $this->assertCount(1, $suite);
    }
    /**
     * @group XXX
     */
    public function testFilter_WhenTwoFiltersAreSet_TwoTestsAreRunning()
    {
        $suite = $this->makeSuite(array('testSimple', 'testToRun', 'testThatWontBeExecuted'));
        $testsToRun = array(
            new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testSimple'),
            new TestCase('Nikoms\FailLover\Tests\FilterTestMock', 'testToRun')
        );
        $this->addFilterOnSuite($suite, $testsToRun);
        $this->assertCount(2, $suite);
    }

} 