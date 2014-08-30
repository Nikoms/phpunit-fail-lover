<?php


namespace Nikoms\FailLover\TestCaseResult;


interface TestCaseInterface {
    /**
     * @param string $separator
     * @return string
     */
    public function getFilter($separator = '/');
} 