<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException;
use Nikoms\FailLover\TestCaseResult\TestCaseRecorderInterface;

class CsvRecorder implements TestCaseRecorderInterface
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
            Columns::CLASS_NAME => $reflectionClass->getName(),
            Columns::METHOD_NAME => $testCase->getName(false),
            Columns::DATA_NAME => '',
            Columns::DATA => '',
        );
        ksort($columns);
        return $columns;
    }
} 