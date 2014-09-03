<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCaseFactory
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return TestCase
     */
    public function createTestCase(\PHPUnit_Framework_TestCase $testCase)
    {
        $reflectionClass = new \ReflectionClass($testCase);
        return new TestCase($reflectionClass->getName(), $testCase->getName(false), $this->getDataName($testCase));
    }


    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return string
     */
    private function getDataName(\PHPUnit_Framework_TestCase $testCase)
    {
        $dataName = '';
        if ($testCase->getName(false) !== $testCase->getName(true)) {
            $dataName = substr($testCase->getName(true), strlen($testCase->getName(false)));
            $dataName = str_replace('with data set', '', $dataName);
            $dataName = $this->removeDataNameDecoration(trim($dataName));

            return $dataName;
        }
        return $dataName;
    }

    /**
     * @param $dataName
     * @return string
     */
    private function removeDataNameDecoration($dataName)
    {
        return ($dataName[0] === '#') ? substr($dataName, 1) : substr($dataName, 1, -1);
    }
} 