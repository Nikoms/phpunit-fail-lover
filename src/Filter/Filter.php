<?php


namespace Nikoms\FailLover\Filter;


use Nikoms\FailLover\TestCaseResult\Storage\ReaderInterface;

class Filter implements FilterInterface
{

    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $filters = array();
        foreach ($this->reader->getList() as $testCase) {
            $filters[] = $testCase->getFilter();
        }

        return implode('|', $filters);
    }
}
