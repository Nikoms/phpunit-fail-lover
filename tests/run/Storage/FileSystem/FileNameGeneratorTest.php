<?php


use Nikoms\FailLover\Storage\FileSystem\FileNameGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileNameGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        include __DIR__ . '/uniqIdMock.php';
    }


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
        $builder = new FileNameGenerator();
        $this->assertSame(FileNameGenerator::BASIC_FILENAME, $builder->create(''));
    }

    public function testCreate_WhenPatternIsAFolder_ReturnTheFolderWithTheStaticFileName()
    {
        $builder = new FileNameGenerator();
        $folder = $this->root->url();
        $this->assertSame($folder . '/' . FileNameGenerator::BASIC_FILENAME, $builder->create($folder));
    }

    public function testCreate_WhenPatternHasDateTimePattern_ReplaceItByTheCurrentDateTime()
    {
        $builder = new FileNameGenerator();
        $this->assertSame(date('Y-m-d-His') . '.csv', $builder->create('{datetime}.csv'));
    }

    public function testCreate_WhenPatternHasUniqId_ReplaceItByAUniqId()
    {
        $builder = new FileNameGenerator();
        $pattern = sprintf('{%s:uniqId}', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId', $builder->create($pattern));
    }

    public function testCreate_WhenPatternHasUniqIdAlreadyTaken_FindTheNextUniqId()
    {
        vfsStream::newFile('xoxoSuperUniqId')
            ->at($this->root);

        $builder = new FileNameGenerator();
        $pattern = sprintf('{%s:uniqId}', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId_1', $builder->create($pattern));
    }

    public function testCreate_WhenPatternHasLast_ReplaceItByTheLastModifiedFile()
    {
        $builder = new FileNameGenerator();

        vfsStream::newFile('first.csv')
            ->at($this->root)
            ->lastModified(100);
        vfsStream::newFile('second.csv')
            ->at($this->root)
            ->lastModified(200);

        $folder = $this->root->url();

        $this->assertSame($folder . '/second.csv', $builder->create('{' . $folder . ':last}'));
        $this->assertSame($folder . '/second.csv', $builder->create('{' . $folder . '/:last}'));
    }

    public function testCreate_WhenPatternHasLastAndThereIsNoFile_ReplaceItByTheDefaultFile()
    {
        $builder = new FileNameGenerator();
        $this->assertSame(
            $this->root->url() . '/' . FileNameGenerator::BASIC_FILENAME,
            $builder->create('{' . $this->root->url() . ':last}')
        );
    }

    public function testCreate_WhenPatternHasLastAndFolderDoesntExist_ThrowsException()
    {
        $builder = new FileNameGenerator();
        $unknownFolder = $this->root->url() . '/unknown_folder';
        $this->setExpectedException('\InvalidArgumentException');
        $builder->create('{' . $unknownFolder . ':last}');
    }

    public function testCreate_WhenPatternHasLastOnFile_ThrowsException()
    {
        $builder = new FileNameGenerator();
        vfsStream::newFile('file.csv')
            ->at($this->root);

        $pathToFile = $this->root->url() . '/file.csv';
        $this->setExpectedException('\InvalidArgumentException');
        $builder->create('{' . $pathToFile . ':last}');
    }
}
