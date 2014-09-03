<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException;
use Nikoms\FailLover\TestCaseResult\RecorderInterface;
use Nikoms\FailLover\TestCaseResult\TestCaseFactory;

class CsvRecorder implements RecorderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param $filePath
     * @throws FileNotCreatedException
     * @throws \InvalidArgumentException
     */
    public function __construct($filePath)
    {
        $filePath = (string) $filePath;
        if($filePath === '' || is_dir($filePath)){
            throw new \InvalidArgumentException();
        }
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            if(@file_put_contents($this->filePath, '') === false){
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
        if(($fp = fopen($this->filePath, 'a')) === false){
            return false;
        }
        $added = false !== fputcsv($fp, $this->getColumns($testCase));
        fclose($fp);
        return $added;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $phpUnitTestCase
     * @return array
     */
    private function getColumns(\PHPUnit_Framework_TestCase $phpUnitTestCase)
    {
        $factory = new TestCaseFactory();
        $testCase = $factory->createTestCase($phpUnitTestCase);
        $columns = array(
            Columns::CLASS_NAME => $testCase->getClassName(),
            Columns::METHOD_NAME => $testCase->getMethod(),
            Columns::DATA_NAME => $testCase->getDataName(),
            Columns::DATA => $testCase->getData()
        );
        ksort($columns);
        return $columns;
    }
} 