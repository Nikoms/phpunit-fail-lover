<?php


namespace Nikoms\FailLover\TestCaseResult;


class FileNameBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate_WhenPatternIsEmpty_ReturnAStaticFileName()
    {
        $builder = new FileNameBuilder();
        $this->assertSame('fail-lover.csv', $builder->create(''));
    }

}
 