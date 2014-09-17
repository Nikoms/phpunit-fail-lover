<?php
namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\Pattern;



use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGenerator;
use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGeneratorInterface;

class DateTimePattern extends RegexPattern implements FileNameGeneratorInterface
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
                return FileNameGenerator::addRightSlash($matches[1]) . date('Y-m-d-His');
            }
        );
    }
}
