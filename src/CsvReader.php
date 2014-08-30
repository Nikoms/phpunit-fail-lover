<?php


namespace Nikoms\FailLover;


use Nikoms\FailLover\TestCaseResult\TestCase;

class CsvReader {
    const CLASS_NAME_COLUMN = 0;
    const METHOD_NAME_COLUMN = 1;
    const DATA_NAME_COLUMN = 2;
    const DATA_COLUMN = 3;

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
                $list[] = new TestCase($data[self::CLASS_NAME_COLUMN], $data[self::METHOD_NAME_COLUMN], $data[self::DATA_NAME_COLUMN], $data[self::DATA_COLUMN]);
            }
            fclose($handle);
        }
        return $list;
    }
} 