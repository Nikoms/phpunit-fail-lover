<?php

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\Pattern;



use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGenerator;
use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGeneratorInterface;

class LastModifiedFilePattern extends RegexPattern implements FileNameGeneratorInterface
{


    /**
     * @return string
     */
    public function getGeneratedFileName()
    {
        return $this->replaceWithCallBack(
            $this->getPattern(),
            'last',
            function ($matches) {
                $dir = FileNameGenerator::addRightSlash($matches[1]);
                $lastModifiedFile = LastModifiedFilePattern::getLastModifiedFile($dir);
                if (empty($lastModifiedFile)) {
                    $lastModifiedFile = FileNameGenerator::BASIC_FILENAME;
                }

                return $dir . $lastModifiedFile;
            }
        );
    }

    /**
     * @param string $dir
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
                    $file != '..'
                    && $file != '.'
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
} 