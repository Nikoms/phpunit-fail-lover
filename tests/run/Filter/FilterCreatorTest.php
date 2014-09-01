<?php


namespace Nikoms\FailLover\Filter;


class FilterCreatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    public function setUp()
    {
        $this->reader = $this->getMockBuilder('Nikoms\FailLover\TestCaseResult\ReaderInterface')
            ->setMethods(array('getList'))
            ->getMock();
    }

    public function testGetFilter_WhenThereIsNoTest_ItReturnsAnEmptyString()
    {
        $this->reader->expects($this->any())->method('getList')->willReturn($this->returnValue(array()));
        $filter = new FilterCreator($this->reader);
        $this->assertSame('', $filter->getFilter());
    }
}
 