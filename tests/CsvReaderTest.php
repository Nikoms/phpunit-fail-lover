<?php


namespace Nikoms\FailLover;


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
}
 