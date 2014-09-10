<?php


namespace Nikoms\FailLover\TestCaseResult;


use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileNameBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup(
            'root',
            null,
            array()
        );
    }

    public function testCreate_WhenPatternIsEmpty_ReturnAStaticFileName()
    {
        $builder = new FileNameBuilder();
        $this->assertSame('fail-lover.csv', $builder->create(''));
    }

    public function testCreate_WhenPatternIsAFolder_ReturnTheFolderWithTheStaticFileName()
    {
        $builder = new FileNameBuilder();
        $folder = $this->root->url();
        $this->assertSame($folder . '/fail-lover.csv', $builder->create($folder));
    }

    public function testCreate_WhenPatternHasDateTimePattern_ReplaceItByTheCurrentDateTime()
    {
        $builder = new FileNameBuilder();
        $this->assertSame(date('Y-m-d-His') . '.csv', $builder->create('{datetime}.csv'));
    }

    public function testCreate_WhenPatternHasUniqId_ReplaceItByAUniqId()
    {
        $builder = new FileNameBuilder();
        $this->assertNotEmpty($builder->create('{uniqId}'));
        $this->assertNotSame('{uniqId}', $builder->create('{uniqId}'));
    }

}
 