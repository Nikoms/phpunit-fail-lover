<?php


namespace Nikoms\FailLover\Filter;


use Nikoms\FailLover\TestCaseResult\TestCase;

class FilterTest extends \PHPUnit_Framework_TestCase {

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
        $this->reader->expects($this->any())->method('getList')->willReturn(array());
        $filter = new Filter($this->reader);
        $this->assertSame('', (string) $filter);
    }

    public function testGetFilter_WhenThereIsOneTest_ItReturnsASimpleFilter()
    {
        $this->reader->expects($this->any())->method('getList')->willReturn(array(
                new TestCase('My\Class\Name','testMethod'),
            ));
        $filter = new Filter($this->reader);
        $this->assertSame('^My\\\\Class\\\\Name\:\:testMethod$', (string) $filter);
    }

    public function testGetFilter_WhenThereIsTwoTests_ItReturnsTwoFiltersSeparatedByAPipe()
    {
        $this->reader->expects($this->any())->method('getList')->willReturn(array(
                new TestCase('My\Class\Name','testMethod'),
                new TestCase('My\Class\Name','testSimple'),
            ));
        $filter = new Filter($this->reader);
        $this->assertSame('^My\\\\Class\\\\Name\:\:testMethod$|^My\\\\Class\\\\Name\:\:testSimple$', (string) $filter);
    }
}
 