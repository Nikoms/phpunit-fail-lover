<?php
namespace MyNameSpace;

class ClassWithMethodThatFailedTest extends \PHPUnit_Framework_TestCase{

    public function testIfWeRunThisTestThenTheFilterWorks()
    {
        $this->assertTrue(true);
    }

    public function testIfWeRunThisTestThenTheFilterDoesNotWork()
    {
        $this->assertTrue(false);
    }

    /**
     * @dataProvider mySimpleDataProvider
     */
    public function testIfWeRunThisTestWithDataProviderThenTheFilterDoesNotWork($firstValue, $secondValue, $dateTime)
    {
        $this->assertTrue(false);
    }

    public function mySimpleDataProvider()
    {
        return array(
            array(
                'first value',
                'second value',
                new \DateTime()
            ),
            'Not so easy /"\'' => array(
                'first value',
                'second value',
                new \DateTime()
            )
        );
    }
} 