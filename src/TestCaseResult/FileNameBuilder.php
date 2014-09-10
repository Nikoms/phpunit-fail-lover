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

        return $newFileName;

    }
}
