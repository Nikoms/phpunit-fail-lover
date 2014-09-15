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
        $newFileName = str_replace('{uniqId}', uniqid(), $newFileName);

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
        return preg_replace_callback(
            '#\{([\w\/\.:]*):last\}#',
            function ($matches) {
                $dir = rtrim($matches[1], '/') . '/';
                $lastFile = FileNameGenerator::BASIC_FILENAME;
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
                } else {
                    throw new \InvalidArgumentException($dir . ' is not a valid folder');
                }

                return $dir . $lastFile;

            },
            $filePath
        );
    }
}
