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
                $list[] = new TestCase($data[Columns::CLASS_NAME], $data[Columns::METHOD_NAME], $data[Columns::DATA_NAME], $data[Columns::DATA]);
            }
            fclose($handle);
        }
        return $list;
    }
} 