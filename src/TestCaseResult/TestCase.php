<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCase implements TestCaseInterface{

    private $className;
    private $method;
    private $dataName;
    private $data;

    public function __construct($className, $method, $dataName = null, $data = null)
    {
        $this->className = $className;
        $this->method = $method;
        $this->dataName = $dataName;
        $this->data = $data;
    }

    public function getName()
    {
        return $this->className.'::'.$this->method . $this->getSuffix();
    }

    private function getSuffix()
    {
        $suffix = '';
        if($this->dataName !== null){
            $prefix = is_numeric($this->dataName) ? '#' : '@';
            $suffix = $prefix . $this->dataName;
        }
        return $suffix;
    }

}