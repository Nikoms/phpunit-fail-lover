<?php


namespace Nikoms\FailLover\TestCaseResult;


class TestCase implements TestCaseInterface
{

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
    private function getName()
    {
        return $this->className . '::' . $this->method . $this->getSuffix();
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getFilter($separator = '/')
    {
        return '^' . preg_quote($this->getName(), $separator) . '$';
    }


    /**
     * @return string
     */
    private function getSuffix()
    {
        if ($this->dataName === null) {
            return '';
        }
        if (is_numeric($this->dataName)) {
            return ' with data set #' . $this->dataName;
        } else {
            return ' with data set "' . $this->dataName . '"';
        }
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return null|string
     */
    public function getDataName()
    {
        return $this->dataName;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
