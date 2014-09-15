<?php


namespace Nikoms\FailLover\Storage\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface;
use Nikoms\FailLover\TestCaseResult\TestCase;

class CsvReader implements ReaderInterface
{

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $list;

    /**
     * @var bool
     */
    private $isValid;


    /**
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->init();
    }

    /**
     * @return \Nikoms\FailLover\TestCaseResult\TestCase[]
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->list);
    }

    private function init()
    {
        $this->list = array();
        $this->isValid = file_exists($this->fileName);

        if (!$this->isValid) {
            return;
        }

        if (($handle = fopen($this->fileName, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $this->list[] = new TestCase(
                    $data[Columns::CLASS_NAME],
                    $data[Columns::METHOD_NAME],
                    $data[Columns::DATA_NAME],
                    $data[Columns::DATA]
                );
            }
            fclose($handle);
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }


}
