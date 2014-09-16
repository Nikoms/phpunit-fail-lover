<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 17/09/2014
 * Time: 01:41
 */

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\Pattern;


use Nikoms\FailLover\Storage\FileSystem\FileNamePattern;

class DateTimePattern extends RegexPattern implements PatternInterface
{

    /**
     * @return string
     */
    public function getGeneratedFileName()
    {
        return $this->replaceWithCallBack(
            $this->getPattern(),
            'datetime',
            function ($matches) {
                return FileNamePattern::addRightSlash($matches[1]) . date('Y-m-d-His');
            }
        );
    }
}