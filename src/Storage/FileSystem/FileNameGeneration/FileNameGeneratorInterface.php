<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 17/09/2014
 * Time: 01:45
 */

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration;


interface FileNameGeneratorInterface
{
    /**
     * @return string
     */
    public function getGeneratedFileName();
} 