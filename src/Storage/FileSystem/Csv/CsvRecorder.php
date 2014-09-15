<?php


namespace Nikoms\FailLover\Storage\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\Exception\OutputNotAvailableException;
use Nikoms\FailLover\TestCaseResult\Storage\RecorderInterface;
use Nikoms\FailLover\TestCaseResult\TestCaseFactory;

class CsvRecorder implements RecorderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param $filePath
     * @throws \InvalidArgumentException
     */
    public function __construct($filePath)
    {
        $filePath = (string)$filePath;

        if ($filePath === '') {
            throw new \InvalidArgumentException();
        }
        if (file_exists($filePath) && is_dir($filePath)) {
            throw new \InvalidArgumentException();
        }

        $this->filePath = $filePath;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     * @throws OutputNotAvailableException
     */
    public function add(\PHPUnit_Framework_TestCase $testCase)
    {
        if (($fp = @fopen($this->filePath, 'a+')) === false) {
            throw new OutputNotAvailableException();
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

    public function clear()
    {
        if (file_exists($this->filePath)) {
            file_put_contents($this->filePath, '');
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        if (file_exists($this->filePath)) {
            return unlink($this->filePath);
        }
        return true;
    }
}
