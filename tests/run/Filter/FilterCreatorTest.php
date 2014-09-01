<?php


namespace Nikoms\FailLover\Filter;


class FilterCreatorTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {

    }
    public function testGetFilter_WhenThereIsNoTest_ItReturnsAnEmptyString()
    {
        $reader = $this->getMockBuilder('Nikoms\FailLover\TestCaseResult\ReaderInterface')
            ->setMethods(array('getList'))
            ->getMock();
        $reader->expects($this->any())->method('getList')->willReturn($this->returnValue(array()));
        $filter = new FilterCreator($reader);
        $this->assertSame('', $filter->getFilter());
    }
}
 