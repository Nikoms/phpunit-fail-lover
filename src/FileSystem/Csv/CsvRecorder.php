<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException;
use Nikoms\FailLover\TestCaseResult\RecorderInterface;

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
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return array
     */
    private function getColumns(\PHPUnit_Framework_TestCase $testCase)
    {
        $reflectionClass = new \ReflectionClass($testCase);
        $columns = array(
            Columns::CLASS_NAME => $reflectionClass->getName(),
            Columns::METHOD_NAME => $testCase->getName(false),
            Columns::DATA_NAME => $this->getDataName($testCase),
            Columns::DATA => '',
        );
        ksort($columns);
        return $columns;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return mixed|string
     */
    private function getDataName(\PHPUnit_Framework_TestCase $testCase)
    {
        $dataName = '';
        if ($testCase->getName(false) !== $testCase->getName(true)) {
            $dataName = substr($testCase->getName(true), strlen($testCase->getName(false)));
            $dataName = str_replace('with data set', '', $dataName);
            $dataName = trim($dataName);
            $dataName = trim($dataName, '#"');
            return $dataName;
        }
        return $dataName;
    }
} 