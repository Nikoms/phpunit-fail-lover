<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\Storage\FileSystem\Csv\CsvRecorder;
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
                'not_empty_file.csv' => 'This file is not empty',
                'one_line.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testSimple,,' . "\n",
                'two_lines.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testSimple,,' . "\n" . '"Nikoms\FailLover\Tests\FilterTestMock",testToRun,,' . "\n",
                'one_line_with_indexed_data_0.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,0,' . "\n",
                'two_lines_with_indexed_data.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,0,' . "\n" . '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,myIndex,' . "\n",
                'one_line_with_double_quote_index.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testWithIndexedDataProvider,"""myIndex""",' . "\n",
            )
        );
    }

    public function testClear()
    {
        $filePath = $this->root->url() . '/not_empty_file.csv';
        $recorder = new CsvRecorder($filePath);
        $recorder->clear();
        $this->assertFileEquals($this->root->url() . '/empty_file.csv', $filePath);
    }

    public function testAdd_WhenTheFileDoesNotExist_TheFileIsCreated()
    {
        $filePath = $this->root->url() . '/unknown_file.csv';
        $recorder = new CsvRecorder($filePath);
        $this->assertFileNotExists($filePath);
        $recorder->add(new FilterTestMock('testSimple'));
        $this->assertFileExists($filePath);
    }

    public function testConstruct_WhenTheFolderDoesNotExists_TheFolderIsCreated()
    {
        $filePath = $this->root->url() . '/unknown_dir/unknown_file.csv';
        $recorder = new CsvRecorder($filePath);
        $recorder->add(new FilterTestMock('testSimple'));
        $this->assertFileExists($filePath);
    }

    public function testConstruct_WhenThePathIsEmpty_AnExceptionOccur()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new CsvRecorder('');
    }

    public function testConstruct_WhenThePathIsAFolder_AnExceptionOccur()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new CsvRecorder($this->root->url());
    }

    public function testAdd_WhenTheGivenFileIsEmpty_OneLineIsAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);
        $this->assertTrue($recorder->add(new FilterTestMock('testSimple')));

        $this->assertFileEquals($this->root->url() . '/one_line.csv', $filePath);
    }

    public function testAdd_WhenAddIsCalledTwice_TwoLinesAreAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $this->assertTrue($recorder->add(new FilterTestMock('testSimple')));
        $this->assertTrue($recorder->add(new FilterTestMock('testToRun')));

        $this->assertFileEquals($this->root->url() . '/two_lines.csv', $filePath);
    }

    public function testAdd_WhenAddAIndexedDataName_TheDataNameIsInAColumn()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $this->assertTrue($recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('no empty data'), '0')));
        $this->assertFileEquals($this->root->url() . '/one_line_with_indexed_data_0.csv', $filePath);
    }

    public function testAdd_WhenAddTwoIndexedDataName_TwoLinesWithDataNameAreAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $this->assertTrue($recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('data 0'), 0)));
        $this->assertTrue($recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('data 1'), 'myIndex')));
        $this->assertFileEquals($this->root->url() . '/two_lines_with_indexed_data.csv', $filePath);
    }

    public function testAdd_WhenAddAnIndexDataNameSurroundedByDoubleQuote_DoubleQuotesArePresent()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);

        $this->assertTrue($recorder->add(new FilterTestMock('testWithIndexedDataProvider',array('data 1'), '"myIndex"')));
        $this->assertFileEquals($this->root->url() . '/one_line_with_double_quote_index.csv', $filePath);
    }

    public function testRemove_WhenAFileExists_TheFileIsRemoved()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new CsvRecorder($filePath);
        $this->assertTrue($recorder->remove());
        $this->assertFileNotExists($filePath);
    }

    public function testRemove_WhenAFileDoesNotExist_ItReturnsTrue()
    {
        $filePath = $this->root->url() . '/unknown_file.csv';
        $recorder = new CsvRecorder($filePath);
        $this->assertTrue($recorder->remove());
    }
}
 