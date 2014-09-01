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

    public function getFilter()
    {
        return '';
    }
} 