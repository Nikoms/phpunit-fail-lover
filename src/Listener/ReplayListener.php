<?php


namespace Nikoms\FailLover\Listener;


use Nikoms\FailLover\Filter\EmptyFilter;
use Nikoms\FailLover\Filter\Filter;
use Nikoms\FailLover\Filter\FilterFactory;
use Nikoms\FailLover\TestCaseResult\ReaderInterface;
use PHPUnit_Framework_TestSuite;

class ReplayListener extends \PHPUnit_Framework_BaseTestListener
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $filterFactory = new FilterFactory();
        $testsToRun = $this->reader->getList();
        if(!empty($testsToRun)){
            $suite->injectFilter($filterFactory->createFactory(new Filter($this->reader)));
        }else{
            $suite->injectFilter($filterFactory->createFactory(new EmptyFilter()));
        }
    }
} 