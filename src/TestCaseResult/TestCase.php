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
        $name = $this->className.'::'.$this->method;
        if($this->dataName !== null){

            if(is_numeric($this->dataName)){
                $name .= ' with data set #' . $this->dataName;
            }else{
                $name .= ' with data set "'.$this->dataName.'"';
            }
        }
        return $name;
    }


}