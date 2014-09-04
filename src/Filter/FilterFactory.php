<?php


namespace Nikoms\FailLover\Filter;


class FilterFactory
{

    /**
     * @param Filter $filter
     * @return \PHPUnit_Runner_Filter_Factory
     */
    public function createFactory(Filter $filter)
    {
        $filterFactory = new \PHPUnit_Runner_Filter_Factory();
        $filterFactory->addFilter(
            new \ReflectionClass('PHPUnit_Runner_Filter_Test'),
            $filter
        );

        return $filterFactory;
    }
}
