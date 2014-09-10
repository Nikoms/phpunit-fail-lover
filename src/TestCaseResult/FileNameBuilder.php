<?php


namespace Nikoms\FailLover\TestCaseResult;


class FileNameBuilder {

    public function create($pattern)
    {
        $pattern = trim((string) $pattern);
        if($pattern === ''){
            return 'fail-lover.csv';
        }
    }
}
