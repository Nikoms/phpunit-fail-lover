<?php


namespace Nikoms\FailLover;


class CsvReaderTest extends \PHPUnit_Framework_TestCase {

    public function testGetList_WhenFileDoesNotExist_TheListIsEmpty()
    {
        $reader = new CsvReader(__DIR__ . '/unknow_file.csv');
        $this->assertCount(0, $reader->getList());
    }

    public function testGetList_WhenFileIsEmpty_TheListIsEmpty()
    {
        $reader = new CsvReader(__DIR__ . '/files/empty_file.csv');
        $this->assertCount(0, $reader->getList());
    }

    public function testGetList_WhenFileHasOneLine_ThereIsOneTest()
    {
        $reader = new CsvReader(__DIR__ . '/files/one_line.csv');
        $list = $reader->getList();
        $this->assertCount(1, $list);
        $this->assertInstanceOf('Nikoms\FailLover\TestCaseResult\TestCaseInterface', $list[0]);
    }
}
 