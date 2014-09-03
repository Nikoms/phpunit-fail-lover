<?php
namespace Nikoms\FailLover\Command;


class ArgumentParser
{
    const PARAMETER_NAME = 'fail-lover=';

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        $actions = array();
        $total = count($this->arguments);
        for ($i = 0; $i < $total; $i++) {
            if ($this->isValidAtIndex($i)) {
                $actions[] = $this->getAtIndex($i);
            }
        }
        return $actions;
    }

    /**
     * @param $i
     * @return string
     */
    private function getAtIndex($i)
    {
        return str_replace(self::PARAMETER_NAME, '', $this->arguments[$i + 1]);
    }

    /**
     * @param $i
     * @return bool
     */
    private function isValidAtIndex($i)
    {
        return $this->arguments[$i] === '-d'
        && isset($this->arguments[$i + 1])
        && $this->isValidParameterName($this->arguments[$i + 1]);
    }

    private function isValidParameterName($parameterName)
    {
        return strpos($parameterName, self::PARAMETER_NAME) === 0;
    }

}