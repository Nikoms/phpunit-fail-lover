<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\Csv;
use Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException;

class TestCaseRecorder implements TestCaseRecorderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param $filePath
     * @throws FileNotCreatedException
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            if(file_put_contents($this->filePath, '') === false){
                throw new FileNotCreatedException();
            }
        }
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     */
    public function add(\PHPUnit_Framework_TestCase $testCase)
    {
        $fp = fopen($this->filePath, 'a');
        $added = false !== fputcsv($fp, $this->getColumns($testCase));
        fclose($fp);
        return $added;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return array
     */
    private function getColumns(\PHPUnit_Framework_TestCase $testCase)
    {
        $reflectionClass = new \ReflectionClass($testCase);
        $columns = array(
            Csv::CLASS_NAME_COLUMN => $reflectionClass->getName(),
            Csv::METHOD_NAME_COLUMN => $testCase->getName(false),
            Csv::DATA_NAME_COLUMN => '',
            Csv::DATA_COLUMN => '',
        );
        ksort($columns);
        return $columns;
    }
} 