<?php


namespace Nikoms\FailLover\Storage\FileSystem;


use Nikoms\FailLover\Storage\FileSystem\Pattern\DateTimePattern;
use Nikoms\FailLover\Storage\FileSystem\Pattern\LastModifiedFilePattern;

class FileNamePattern
{

    const BASIC_FILENAME = 'fail-lover.txt';

    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $dir
     * @return string
     */
    public static function addRightSlash($dir)
    {
        return rtrim($dir, '/') . '/';
    }


    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }


    /**
     * @param string $fileName
     * @return string
     * @throw \InvalidArgumentException
     */
    private function replaceLastModifiedFile($fileName)
    {
        $dateTimePattern = new LastModifiedFilePattern($fileName);
        return $dateTimePattern->getGeneratedFileName();
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function replaceUniqId($fileName)
    {
        return $this->replaceWithCallBack(
            $fileName,
            'uniqId',
            function ($matches) {
                $dir = rtrim($matches[1], '/') . '/';
                $uniqId = uniqid();
                $fileName = $uniqId;
                $i = 1;
                while (file_exists($dir . $fileName)) {
                    $fileName = $uniqId . '_' . $i;
                    $i++;
                }

                return $dir . $fileName;
            }
        );
    }

    /**
     * @param string $fileName
     * @param string $param
     * @param \Closure $function
     * @return mixed
     */
    private function replaceWithCallBack($fileName, $param, $function)
    {
        return preg_replace_callback(
            '#([\w\/\.:]*):' . $param . '#',
            $function,
            $fileName
        );
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function replaceDateTime($fileName)
    {
        $dateTimePattern = new DateTimePattern($fileName);
        return $dateTimePattern->getGeneratedFileName();
    }

    public function getGeneratedFileName()
    {
        if ($this->pattern === '') {
            return self::BASIC_FILENAME;
        }
        if (file_exists($this->pattern) && is_dir($this->pattern)) {
            return $this->pattern . '/' . self::BASIC_FILENAME;
        }

        $newFileName = $this->replaceDateTime($this->pattern);
        $newFileName = $this->replaceUniqId($newFileName);
        $newFileName = $this->replaceLastModifiedFile($newFileName);

        return $newFileName;
    }
}

