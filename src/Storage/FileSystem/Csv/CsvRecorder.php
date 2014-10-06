<?php


namespace Nikoms\FailLover\Storage\FileSystem\Csv;


use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGeneratorInterface;
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
     * @param string|FileNameGeneratorInterface $filePath
     * @throws \InvalidArgumentException
     */
    public function __construct($filePath)
    {
        $this->initFilePath($filePath);
        $this->checkFilePath();
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     * @throws OutputNotAvailableException
     */
    public function add(\PHPUnit_Framework_TestCase $testCase)
    {
        $this->createRecordFile();
        if (($fp = @fopen($this->filePath, 'a')) === false) {
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

    private function createRecordFile()
    {
        if (!file_exists($this->filePath)) {
            $pathInfo = pathinfo($this->filePath);
            $folderPath = $pathInfo['dirname'];
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            file_put_contents($this->filePath, '');
        }
    }

    /**
     * @param string|FileNameGeneratorInterface $filePath
     * @return string
     */
    private function initFilePath($filePath)
    {
        if ($filePath instanceof FileNameGeneratorInterface) {
            $filePath = $filePath->getGeneratedFileName();
        }

        $this->filePath = (string)$filePath;
    }

    private function checkFilePath()
    {
        if ($this->filePath === '') {
            throw new \InvalidArgumentException();
        }
        if (file_exists($this->filePath) && is_dir($this->filePath)) {
            throw new \InvalidArgumentException();
        }
    }
}
