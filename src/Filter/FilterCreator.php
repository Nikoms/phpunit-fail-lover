<?php


namespace Nikoms\FailLover\Filter;


use Nikoms\FailLover\TestCaseResult\ReaderInterface;

class FilterCreator {

    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(ReaderInterface $reader){
        $this->reader = $reader;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        $filters = array();
        foreach ($this->reader->getList() as $testCase) {
            $filters[] = $testCase->getFilter();
        }
        return implode('|', $filters);
    }
} 