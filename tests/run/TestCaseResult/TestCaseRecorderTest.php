<?php


namespace Nikoms\FailLover\TestCaseResult;


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
//                'empty_file.csv',
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
}
 