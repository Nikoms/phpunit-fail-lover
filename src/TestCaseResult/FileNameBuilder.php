<?php


namespace Nikoms\FailLover\TestCaseResult;


class FileNameBuilder
{

    const BASIC_CSV_FILENAME = 'fail-lover.csv';

    public function create($pattern)
    {
        $pattern = trim((string)$pattern);
        if ($pattern === '') {
            return self::BASIC_CSV_FILENAME;
        }
        if (file_exists($pattern) && is_dir($pattern)) {
            return $pattern . '/' . self::BASIC_CSV_FILENAME;
        }
    }
}
