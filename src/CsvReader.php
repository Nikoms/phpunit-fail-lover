<?php


namespace Nikoms\FailLover;


use Nikoms\FailLover\TestCaseResult\TestCase;

class CsvReader {
    const CLASS_NAME = 0;
    const METHOD_NAME = 1;
    const DATA = 2;
    const DATA_NAME = 3;

    /**
     * @var string
     */
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function getList()
    {
        if(!file_exists($this->fileName)){
            return array();
        }

        $list = array();
        if (($handle = fopen($this->fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $list[] = new TestCase($data[self::CLASS_NAME], $data[self::METHOD_NAME], $data[self::DATA_NAME], $data[self::DATA]);
            }
            fclose($handle);
        }
        return $list;
    }
} 