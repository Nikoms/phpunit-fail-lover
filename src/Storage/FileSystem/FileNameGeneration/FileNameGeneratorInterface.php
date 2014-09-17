<?php

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration;


interface FileNameGeneratorInterface
{
    /**
     * @return string
     */
    public function getGeneratedFileName();
}
