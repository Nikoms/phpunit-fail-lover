<?php


namespace Nikoms\FailLover\Command;


class ArgumentParserTest extends \PHPUnit_Framework_TestCase
{

    public function testGetActions_GivenEmptyArray_ReturnNoAction()
    {
        $parser = new ArgumentParser(array());
        $this->assertSame(array(), $parser->getActions());
    }

    public function testGetActions_GivenUselessParameters_ReturnNoAction()
    {
        $parser = new ArgumentParser(array('arg1', 'arg2', 'arg3'));
        $this->assertSame(array(), $parser->getActions());
    }

    public function testGetActions_GivenOneUsefullParameterWithoutPrefix_ReturnNoAction()
    {
        $parser = new ArgumentParser(array('fail-lover=log'));
        $this->assertSame(array(), $parser->getActions());
    }

    public function testGetActions_GivenOneUsefullParameterWithPrefix_ReturnOneAction()
    {
        $parser = new ArgumentParser(array('-d','fail-lover=param'));
        $this->assertSame(array('param'), $parser->getActions());
    }

    public function testGetActions_GivenOnlyPrefix_ReturnNoAction()
    {
        $parser = new ArgumentParser(array('-d'));
        $this->assertSame(array(), $parser->getActions());
    }

    public function testGetActions_GivenOneUselessParameterWithPrefix_ReturnNoAction()
    {
        $parser = new ArgumentParser(array('-d','useless=parameter'));
        $this->assertSame(array(), $parser->getActions());
    }

    public function testGetActions_GivenTwoUsefullParametersWithPrefix_ReturnTwoActions()
    {
        $parser = new ArgumentParser(array('-d', 'fail-lover=param1', '-d', 'fail-lover=param2'));
        $this->assertSame(array('param1','param2'), $parser->getActions());
    }

    public function testGetActions_GivenTwoUsefullParametersWithPrefixAndAUselessParameter_ReturnTwoActions()
    {
        $parser = new ArgumentParser(array('-d', 'fail-lover=param1', 'uselesss', '-d', 'fail-lover=param2'));
        $this->assertSame(array('param1','param2'), $parser->getActions());
    }
}
 