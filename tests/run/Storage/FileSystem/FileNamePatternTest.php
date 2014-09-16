<?php


use Nikoms\FailLover\Storage\FileSystem\FileNamePattern;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileNamePatternTest extends \PHPUnit_Framework_TestCase
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
     * @return FileNamePattern
     */
    private function getFileNamePattern($fileName)
    {
        return new FileNamePattern($fileName);
    }

    public function testCreate_WhenPatternIsEmpty_ReturnAStaticFileName()
    {
        $this->assertSame(FileNamePattern::BASIC_FILENAME, $this->getFileNamePattern('')->getGeneratedFileName());
    }

    public function testCreate_WhenPatternIsAFolder_ReturnTheFolderWithTheStaticFileName()
    {
        $folder = $this->root->url();
        $this->assertSame($folder . '/' . FileNamePattern::BASIC_FILENAME, $this->getFileNamePattern($folder)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasDateTimePattern_ReplaceItByTheCurrentDateTime()
    {
        $pattern = sprintf('%s:datetime', $this->root->url());
        $this->assertSame($this->root->url() . '/' . date('Y-m-d-His'), $this->getFileNamePattern($pattern)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasUniqId_ReplaceItByAUniqId()
    {
        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId', $this->getFileNamePattern($pattern)->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasUniqIdAlreadyTaken_FindTheNextUniqId()
    {
        vfsStream::newFile('xoxoSuperUniqId')
            ->at($this->root);

        $pattern = sprintf('%s:uniqId', $this->root->url());
        $this->assertSame($this->root->url() . '/xoxoSuperUniqId_1', $this->getFileNamePattern($pattern)->getGeneratedFileName());
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

        $this->assertSame($folder . '/second.csv', $this->getFileNamePattern($folder . ':last')->getGeneratedFileName());
        $this->assertSame($folder . '/second.csv', $this->getFileNamePattern($folder . '/:last')->getGeneratedFileName());
    }

    public function testCreate_WhenPatternHasLastAndThereIsNoFile_ReplaceItByTheDefaultFile()
    {
        $this->assertSame(
            $this->root->url() . '/' . FileNamePattern::BASIC_FILENAME,
            $this->getFileNamePattern($this->root->url() . ':last')->getGeneratedFileName()
        );
    }

    public function testCreate_WhenPatternHasLastAndFolderDoesntExist_ThrowsException()
    {
        $unknownFolder = $this->root->url() . '/unknown_folder';
        $this->setExpectedException('\InvalidArgumentException');
        $this->getFileNamePattern($unknownFolder . ':last')->getGeneratedFileName();
    }

    public function testCreate_WhenPatternHasLastOnFile_ThrowsException()
    {
        vfsStream::newFile('file.csv')
            ->at($this->root);

        $pathToFile = $this->root->url() . '/file.csv';
        $this->setExpectedException('\InvalidArgumentException');
        $this->getFileNamePattern($pathToFile . ':last')->getGeneratedFileName();
    }
}
