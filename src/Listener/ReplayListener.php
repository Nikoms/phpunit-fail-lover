<?php


namespace Nikoms\FailLover\Listener;


use Nikoms\FailLover\Command\ArgumentParser;
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
    /**
     * @var ArgumentParser
     */
    private $parser;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->parser = new ArgumentParser($_SERVER['argv']);
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (!$this->parser->hasAction('replay')) {
            return;
        }

        $filterFactory = new FilterFactory();
        if (!$this->reader->isEmpty()) {
            $suite->injectFilter($filterFactory->createFactory(new Filter($this->reader)));
        } else {
            $suite->injectFilter($filterFactory->createFactory(new EmptyFilter()));
        }
    }
}
