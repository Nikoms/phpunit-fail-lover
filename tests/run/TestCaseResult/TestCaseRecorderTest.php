<?php


namespace Nikoms\FailLover\TestCaseResult;


use Nikoms\FailLover\Tests\FilterTestMock;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class TestCaseRecorderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root',null, array(
                'empty_file.csv' => '',
                'empty_file_after_add_one_line.csv' => '"Nikoms\FailLover\Tests\FilterTestMock",testSimple,,' . "\n",
//                'one_line.csv' => '"className","method","dataName","data"',
//                'two_lines.csv' => '"className1","method1","dataName1","data1"'.PHP_EOL.'"className2","method2","dataName2","data2"',
            )
        );
    }

    public function testContruct_WhenTheFileDoesNotExist_TheFileIsCreated()
    {
        $filePath = $this->root->url() . '/unknown_file.csv';
        new TestCaseRecorder($filePath);
        $this->assertFileExists($filePath);
    }

    public function testAdd_WhenTheFileIsEmpty_OneLineIsAdded()
    {
        $filePath = $this->root->url() . '/empty_file.csv';
        $recorder = new TestCaseRecorder($filePath);
        $testCase = new FilterTestMock('testSimple');

        $recorder->add($testCase);

        $this->assertFileEquals($this->root->url() . '/empty_file_after_add_one_line.csv', $filePath);
    }
}
 