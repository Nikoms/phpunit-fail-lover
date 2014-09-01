<?php


namespace Nikoms\FailLover\FileSystem\Csv;


use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class CsvReaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root',null, array(
                'empty_file.csv',
                'one_line.csv' => '"className","method","dataName","data"',
                'two_lines.csv' => '"className1","method1","dataName1","data1"'.PHP_EOL.'"className2","method2","dataName2","data2"',
            )
        );
    }

    public function testGetList_WhenFileDoesNotExist_TheListIsEmpty()
    {
        $reader = new CsvReader($this->root->url() . '/unknow_file.csv');
        $this->assertCount(0, $reader->getList());
    }

    public function testGetList_WhenFileIsEmpty_TheListIsEmpty()
    {
        $reader = new CsvReader($this->root->url() . '/empty_file.csv');
        $this->assertCount(0, $reader->getList());
    }

    public function testGetList_WhenFileHasOneLine_ThereIsOneTest()
    {
        $reader = new CsvReader($this->root->url() . '/one_line.csv');
        $list = $reader->getList();
        $this->assertCount(1, $list);
        $this->assertInstanceOf('Nikoms\FailLover\TestCaseResult\TestCaseInterface', $list[0]);
    }

    public function testGetList_WhenFileHasTwoLines_ThereIsTwoTests()
    {
        $reader = new CsvReader($this->root->url() . '/two_lines.csv');
        $list = $reader->getList();
        $this->assertCount(2, $list);
    }
}
 