<?php


namespace Nikoms\FailLover\Storage\FileSystem;


class FileNameGenerator
{

    const BASIC_FILENAME = 'fail-lover.txt';

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

        $newFileName = str_replace('{datetime}', date('Y-m-d-His'), $fileName);
        $newFileName = $this->replaceUniqId($newFileName);
        $newFileName = $this->replaceLastModifiedFile($newFileName);

        return $newFileName;
    }

    /**
     * @param string $filePath
     * @return string
     * @throw \InvalidArgumentException
     */
    private function replaceLastModifiedFile($filePath)
    {
        return $this->replaceWithCallBack(
            $filePath,
            'last',
            function ($matches) {
                $dir = FileNameGenerator::addRightSlash($matches[1]);

                return $dir . FileNameGenerator::getLastModifiedFile($dir);
            }
        );
    }

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
     * @param string $filePath
     * @return string
     */
    private function replaceUniqId($filePath)
    {
        return $this->replaceWithCallBack(
            $filePath,
            'uniqId',
            function ($matches) {
                return rtrim($matches[1], '/') . '/' . uniqid();
            }
        );
    }

    /**
     * @param $filePath
     * @param $param
     * @param $function
     * @return mixed
     */
    private function replaceWithCallBack($filePath, $param, $function)
    {
        return preg_replace_callback(
            '#\{([\w\/\.:]*):' . $param . '\}#',
            $function,
            $filePath
        );
    }
}

