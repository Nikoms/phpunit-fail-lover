<?php


namespace Nikoms\FailLover\TestCaseResult;


class FileNameBuilder
{

    const BASIC_CSV_FILENAME = 'fail-lover.csv';

    public function create($pattern)
    {
        $fileName = trim((string)$pattern);
        if ($fileName === '') {
            return self::BASIC_CSV_FILENAME;
        }
        if (file_exists($fileName) && is_dir($fileName)) {
            return $fileName . '/' . self::BASIC_CSV_FILENAME;
        }

        $newFileName = str_replace('{datetime}', date('Y-m-d-His'), $fileName);
        $newFileName = str_replace('{uniqId}', uniqid(), $newFileName);

        $newFileName = $this->replaceLastModifiedFile($newFileName);

        return $newFileName;

    }

    /**
     * @param $filePath
     * @return mixed
     */
    private function replaceLastModifiedFile($filePath)
    {
        return preg_replace_callback('#\{([\w\/:]*):last\}#',function($matches){
                $dir = rtrim($matches[1], '/') . '/';
                $lastFile = self::BASIC_CSV_FILENAME;
                $lastModifiedTime = 0;

                if (is_dir($dir)) {
                    if ($dh = opendir($dir)) {
                        while (($file = readdir($dh)) !== false) {
                            $filePath = $dir . $file;
                            if(is_file($filePath)){
                                $modifiedTime = filemtime($filePath);
                                if($lastModifiedTime < $modifiedTime){
                                    $lastFile = $file;
                                    $lastModifiedTime = $modifiedTime;
                                }
                            }
                        }
                        closedir($dh);
                    }
                }
                return $dir . $lastFile;

            }, $filePath);
    }
}
