<?php


namespace Nikoms\FailLover\Storage\FileSystem;


class Directory
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return bool
     */
    private function exists()
    {
        return file_exists($this->dir) && is_dir($this->dir);
    }

    /**
     * @return string
     */
    public function getLastModifiedFileName()
    {
        if (!$this->exists()) {
            throw new \InvalidArgumentException($this->dir . ' is not a valid folder');
        }

        $lastFile = '';
        $lastModifiedTime = 0;

        foreach ($this->getFiles() as $file) {
            if ($this->isFileMoreRecent($file, $lastModifiedTime)) {
                $lastFile = $file;
                $lastModifiedTime = filemtime($this->dir . $file);
            }
        }

        return $lastFile;
    }

    /**
     * @return array
     */
    private function getFiles()
    {
        $files = array();
        $dh = opendir($this->dir);
        while (($file = readdir($dh)) !== false) {
            if ($this->isFileValid($file)) {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * @param $file
     * @return bool
     */
    private function isFileValid($file)
    {
        return $file != '..' && $file != '.' && is_file($this->dir . $file);
    }

    /**
     * @param $file
     * @param $lastModifiedTime
     * @return bool
     */
    private function isFileMoreRecent($file, $lastModifiedTime)
    {
        return $lastModifiedTime < filemtime($this->dir . $file);
    }
}
