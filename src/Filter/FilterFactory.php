<?php


namespace Nikoms\FailLover\Filter;


class FilterFactory
{

    /**
     * @param FilterInterface $filter
     * @return \PHPUnit_Runner_Filter_Factory
     */
    public function createFactory(FilterInterface $filter)
    {
        $filterFactory = new \PHPUnit_Runner_Filter_Factory();
        $filterFactory->addFilter(
            new \ReflectionClass('PHPUnit_Runner_Filter_Test'),
            (string)$filter
        );

        return $filterFactory;
    }
}
