<?php


use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGenerator;
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

    /**
     * @param string$fileName
     * @return FileNameGenerator
     */
    private function getFileNameGenerator($fileName)
    {
        return new FileNameGenerator($fileName);
    }

    public function testCreate_WhenPatternIsEmpty_ReturnAStaticFileName()
    {
        $this->assertSame(FileNameGenerator::BASIC_FILENAME, $this->getFileNameGenerator('')->getGeneratedFileName());
    }

    public function testCreate_WhenPatternIsAFolder_ReturnTheFolderWithTheStaticFileName()
    {
        $folder = $this->root->url();
        $this->assertSame($folder . '/' . FileNameGenerator::BASIC_FILENAME, $this->getFileNameGenerator($folder)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasDateTimePattern_ReplaceItByTheCurrentDateTime()
    {
        $pattern = sprintf('%s:datetime', $this->root->url());
        $this->assertSame($this->root->url() . '/' . date('Y-m-d-His'), $this->getFileNameGenerator($pattern)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasUniqId_ReplaceItByAUniqId()
    {
        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId', $this->getFileNameGenerator($pattern)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasUniqIdAlreadyTaken_FindTheNextUniqId()
    {
        vfsStream::newFile('xoxoSuperUniqId')
            ->at($this->root);

        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId_1', $this->getFileNameGenerator($pattern)->getGeneratedFileName());
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

        $this->assertSame($folder . '/second.csv', $this->getFileNameGenerator($folder . ':last')->getGeneratedFileName());
        $this->assertSame($folder . '/second.csv', $this->getFileNameGenerator($folder . '/:last')->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasLastAndThereIsNoFile_ReplaceItByTheDefaultFile()
    {
        $this->assertSame(
            $this->root->url() . '/' . FileNameGenerator::BASIC_FILENAME,
            $this->getFileNameGenerator($this->root->url() . ':last')->getGeneratedFileName()
        );
    }

    public function testCreate_WhenPatternHasLastAndFolderDoesntExist_ThrowsException()
    {
        $unknownFolder = $this->root->url() . '/unknown_folder';
        $this->setExpectedException('\InvalidArgumentException');
        $this->getFileNameGenerator($unknownFolder . ':last')->getGeneratedFileName();
    }

    public function testCreate_WhenPatternHasLastOnFile_ThrowsException()
    {
        vfsStream::newFile('file.csv')
            ->at($this->root);

        $pathToFile = $this->root->url() . '/file.csv';
        $this->setExpectedException('\InvalidArgumentException');
        $this->getFileNameGenerator($pathToFile . ':last')->getGeneratedFileName();
    }
}
