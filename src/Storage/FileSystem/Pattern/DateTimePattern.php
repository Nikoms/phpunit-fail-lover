<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 17/09/2014
 * Time: 01:41
 */

namespace Nikoms\FailLover\Storage\FileSystem\Pattern;


use Nikoms\FailLover\Storage\FileSystem\FileNamePattern;

class DateTimePattern implements PatternInterface
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
     * @return string
     */
    public function getGeneratedFileName()
    {
        return $this->replaceWithCallBack(
            $this->pattern,
            'datetime',
            function ($matches) {
                return FileNamePattern::addRightSlash($matches[1]) . date('Y-m-d-His');
            }
        );
    }

    /**
     * @param string $fileName
     * @param string $param
     * @param \Closure $function
     * @return mixed
     */
    private function replaceWithCallBack($fileName, $param, $function)
    {
        return preg_replace_callback(
            '#([\w\/\.:]*):' . $param . '#',
            $function,
            $fileName
        );
    }
}