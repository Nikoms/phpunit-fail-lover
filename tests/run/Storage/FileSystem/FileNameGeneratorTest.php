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

    /**
     * @var FileNameGenerator
     */
    private $fileNameGenerator;

    public function setUp()
    {
        $this->fileNameGenerator = new FileNameGenerator();
        $this->root = vfsStream::setup(
            'root',
            null,
            array()
        );
    }

    public function testCreate_WhenPatternIsEmpty_ReturnAStaticFileName()
    {
        $this->assertSame(FileNameGenerator::BASIC_FILENAME, $this->fileNameGenerator->create(''));
    }

    public function testCreate_WhenPatternIsAFolder_ReturnTheFolderWithTheStaticFileName()
    {
        $folder = $this->root->url();
        $this->assertSame($folder . '/' . FileNameGenerator::BASIC_FILENAME, $this->fileNameGenerator->create($folder));
    }

    public function testCreate_WhenPatternHasDateTimePattern_ReplaceItByTheCurrentDateTime()
    {
        $pattern = sprintf('%s:datetime', $this->root->url());
        $this->assertSame($this->root->url() . '/' . date('Y-m-d-His'), $this->fileNameGenerator->create($pattern));
    }

    public function testCreate_WhenPatternHasUniqId_ReplaceItByAUniqId()
    {
        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId', $this->fileNameGenerator->create($pattern));
    }

    public function testCreate_WhenPatternHasUniqIdAlreadyTaken_FindTheNextUniqId()
    {
        vfsStream::newFile('xoxoSuperUniqId')
            ->at($this->root);

        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId_1', $this->fileNameGenerator->create($pattern));
    }

    public function testCreate_WhenPatternHasLast_ReplaceItByTheLastModifiedFile()
    {
        vfsStream::newFile('first.csv')
            ->at($this->root)
            ->lastModified(100);
        vfsStream::newFile('second.csv')
            ->at($this->root)
            ->lastModified(200);

        $folder = $this->root->url();

        $this->assertSame($folder . '/second.csv', $this->fileNameGenerator->create($folder . ':last'));
        $this->assertSame($folder . '/second.csv', $this->fileNameGenerator->create($folder . '/:last'));
    }

    public function testCreate_WhenPatternHasLastAndThereIsNoFile_ReplaceItByTheDefaultFile()
    {
        $this->assertSame(
            $this->root->url() . '/' . FileNameGenerator::BASIC_FILENAME,
            $this->fileNameGenerator->create($this->root->url() . ':last')
        );
    }

    public function testCreate_WhenPatternHasLastAndFolderDoesntExist_ThrowsException()
    {
        $unknownFolder = $this->root->url() . '/unknown_folder';
        $this->setExpectedException('\InvalidArgumentException');
        $this->fileNameGenerator->create($unknownFolder . ':last');
    }

    public function testCreate_WhenPatternHasLastOnFile_ThrowsException()
    {
        vfsStream::newFile('file.csv')
            ->at($this->root);

        $pathToFile = $this->root->url() . '/file.csv';
        $this->setExpectedException('\InvalidArgumentException');
        $this->fileNameGenerator->create($pathToFile . ':last');
    }
}
