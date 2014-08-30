<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCase implements TestCaseInterface{

    private $className;
    private $name;
    private $dataName;
    private $data;

    public function __construct($className, $name, $dataName, $data)
    {
        $this->className = $className;
        $this->name = $name;
        $this->dataName = $dataName;
        $this->data = $data;
    }

    public function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function getData()
    {
        // TODO: Implement getData() method.
    }

    public function getDataName()
    {
        // TODO: Implement getDataName() method.
    }
}