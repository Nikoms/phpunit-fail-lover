<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCase implements TestCaseInterface{

    private $className;
    private $method;
    private $dataName;
    private $data;

    /**
     * @param string $className
     * @param string $method
     * @param string $dataName
     * @param mixed $data
     */
    public function __construct($className, $method, $dataName = null, $data = null)
    {
        $this->className = $className;
        $this->method = $method;
        $this->dataName = $dataName;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->className.'::'.$this->method . $this->getSuffix();
    }

    /**
     * @return string
     */
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