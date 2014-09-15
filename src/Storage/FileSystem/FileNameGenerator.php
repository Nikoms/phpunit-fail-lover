<?php


namespace Nikoms\FailLover\Storage\FileSystem;


class FileNameGenerator
{

    const BASIC_FILENAME = 'fail-lover.txt';

    /**
     * @param string $dir
     * @return string
     */
    public static function addRightSlash($dir)
    {
        return rtrim($dir, '/') . '/';
    }

    /**
     * @param $dir
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function getLastModifiedFile($dir)
    {
        $lastFile = '';
        $lastModifiedTime = 0;

        if (file_exists($dir) && is_dir($dir) && $dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                $currentFilePath = $dir . $file;
                if (
                    $currentFilePath != '..'
                    && $currentFilePath != '.'
                    && is_file($currentFilePath)
                    && $lastModifiedTime < filemtime($currentFilePath)
                ) {
                    $lastFile = $file;
                    $lastModifiedTime = filemtime($currentFilePath);
                }
            }
            closedir($dh);

            return $lastFile;
        } else {
            throw new \InvalidArgumentException($dir . ' is not a valid folder');
        }
    }

    /**
     * @param string $pattern
     * @return string
     * @throw \InvalidArgumentException
     */
    public function create($pattern)
    {
        $fileName = trim((string)$pattern);
        if ($fileName === '') {
            return self::BASIC_FILENAME;
        }
        if (file_exists($fileName) && is_dir($fileName)) {
            return $fileName . '/' . self::BASIC_FILENAME;
        }

        $newFileName = $this->replaceDateTime($fileName);
        $newFileName = $this->replaceUniqId($newFileName);
        $newFileName = $this->replaceLastModifiedFile($newFileName);

        return $newFileName;
    }

    /**
     * @param string $fileName
     * @return string
     * @throw \InvalidArgumentException
     */
    private function replaceLastModifiedFile($fileName)
    {
        return $this->replaceWithCallBack(
            $fileName,
            'last',
            function ($matches) {
                $dir = FileNameGenerator::addRightSlash($matches[1]);
                $lastModifiedFile = FileNameGenerator::getLastModifiedFile($dir);
                if (empty($lastModifiedFile)) {
                    $lastModifiedFile = FileNameGenerator::BASIC_FILENAME;
                }

                return $dir . $lastModifiedFile;
            }
        );
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
     * @param $fileName
     * @param $param
     * @param $function
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
     * @param $fileName
     * @return mixed
     */
    private function replaceDateTime($fileName)
    {
        return $this->replaceWithCallBack(
            $fileName,
            'datetime',
            function ($matches) {
                return FileNameGenerator::addRightSlash($matches[1]) . date('Y-m-d-His');
            }
        );
    }
}

