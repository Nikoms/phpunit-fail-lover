<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\TestCase;

class CsvReader {

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return array
     */
    public function getList()
    {
        if(!file_exists($this->fileName)){
            return array();
        }

        $list = array();
        if (($handle = fopen($this->fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $list[] = new TestCase($data[Csv::CLASS_NAME_COLUMN], $data[Csv::METHOD_NAME_COLUMN], $data[Csv::DATA_NAME_COLUMN], $data[Csv::DATA_COLUMN]);
            }
            fclose($handle);
        }
        return $list;
    }
} 