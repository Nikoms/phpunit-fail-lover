<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException;

class TestCaseRecorder
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            if(file_put_contents($this->filePath, '') === false){
                throw new FileNotCreatedException();
            }
        }
    }

    public function add(\PHPUnit_Framework_TestCase $testCase)
    {
        $reflectionClass = new \ReflectionClass($testCase);
        $columns = array(
            $reflectionClass->getName(),
            $testCase->getName(false),
            '',
            '',
        );

        $fp = fopen($this->filePath, 'a');
        fputcsv($fp, $columns);
        fclose($fp);
    }
} 