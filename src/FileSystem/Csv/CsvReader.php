<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use Nikoms\FailLover\TestCaseResult\ReaderInterface;
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
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->initList(); //This need to be done here, otherwise, it's impossible to get the list, because "Recorder" empties the file in his constructor too
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

    private function initList()
    {
        $this->list = array();

        if (!file_exists($this->fileName)) {
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


}
