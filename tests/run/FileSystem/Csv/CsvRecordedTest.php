<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\FileSystem\Csv\CsvRecorder;
use Nikoms\FailLover\Tests\FilterTestMock;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class CsvRecorderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root',null, array(
                'empty_file.csv' => '',
                'empty_file_after_add_one_line.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testSimple,,' . "\n",
                'empty_file_after_add_two_lines.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testSimple,,' . "\n" . '"Nikoms\FailLover\Tests\FilterTestMock",testToRun,,' . "\n",
                'empty_file_after_add_one_line_with_indexed_data_0.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,0,' . "\n",
                'empty_file_after_add_two_lines_with_indexed_data.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,0,' . "\n" . '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,1,' . "\n",
            )
        );
    }

    public function testContruct_WhenTheFileDoesNotExist_TheFileIsCreated()
    {
        $filePath = $this->root->url() . '/unknown_file.csv';
        new CsvRecorder($filePath);
        $this->assertFileExists($filePath);
    }

    public function testConstruct_WhenTheFileIsEmpty_AnExceptionOccur()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new CsvRecorder('');
    }

    public function testConstruct_WhenTheFileIsAFolder_AnExceptionOccur()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new CsvRecorder($this->root->url());
    }

    public function testConstruct_WhenTheFolderDoesNotExists_AnExceptionOccur()
    {
        $this->setExpectedException('Nikoms\FailLover\TestCaseResult\Exception\FileNotCreatedException');
        new CsvRecorder($this->root->url() . '/unknown_dir/unknown_file.csv');
    }

    public function testAdd_WhenTheFileIsEmpty_OneLineIsAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);
        $recorder->add(new FilterTestMock('testSimple'));

        $this->assertFileEquals($this->root->url() . '/empty_file_after_add_one_line.csv', $filePath);
    }

    public function testAdd_WhenAddIsCalledTwice_TwoLinesAreAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $recorder->add(new FilterTestMock('testSimple'));
        $recorder->add(new FilterTestMock('testToRun'));

        $this->assertFileEquals($this->root->url() . '/empty_file_after_add_two_lines.csv', $filePath);
    }

    public function testAdd_WhenAddAIndexedDataName_TheDataNameIsInAColumn()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('no empty data'), '0'));
        $this->assertFileEquals($this->root->url() . '/empty_file_after_add_one_line_with_indexed_data_0.csv', $filePath);
    }

    public function testAdd_WhenAddTwoIndexedDataName_TwoLinesWithDataNameAreAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('data 0'), '0'));
        $recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('data 1'), '1'));
        $this->assertFileEquals($this->root->url() . '/empty_file_after_add_two_lines_with_indexed_data.csv', $filePath);
    }
}
 