<?php

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\Pattern;


abstract class RegexPattern
{

    /**
     * @var
     */
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return mixed
     */
    protected function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $fileName
     * @param string $param
     * @param \Closure $function
     * @return mixed
     */
    protected function replaceWithCallBack($fileName, $param, $function)
    {
        return preg_replace_callback(
            '#([\w\/\.:]*):' . $param . '#',
            $function,
            $fileName
        );
    }
} 