<?php
namespace Nikoms\FailLover\Command;


class ArgumentParser
{
    const PARAMETER_NAME = 'fail-lover=';
    const COMMAND_PREFIX = '-d';

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
     * @param int $i Index of the COMMAND_PREFIX
     * @return string
     */
    private function getAtIndex($i)
    {
        return str_replace(self::PARAMETER_NAME, '', $this->arguments[$i + 1]);
    }

    /**
     * @param int $i Index of the COMMAND_PREFIX
     * @return bool
     */
    private function isValidAtIndex($i)
    {
        return $this->arguments[$i] === self::COMMAND_PREFIX
        && isset($this->arguments[$i + 1])
        && $this->isValidParameterName($this->arguments[$i + 1]);
    }

    /**
     * @param string $parameterName
     * @return bool
     */
    private function isValidParameterName($parameterName)
    {
        return strpos($parameterName, self::PARAMETER_NAME) === 0;
    }

}