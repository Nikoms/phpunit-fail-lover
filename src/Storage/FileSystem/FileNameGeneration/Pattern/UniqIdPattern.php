<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 17/09/2014
 * Time: 01:51
 */

namespace Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\Pattern;


use Nikoms\FailLover\Storage\FileSystem\FileNameGeneration\FileNameGeneratorInterface;

class UniqIdPattern  extends RegexPattern implements FileNameGeneratorInterface
{

    /**
     * @return string
     */
    public function getGeneratedFileName()
    {
        return $this->replaceWithCallBack(
            $this->getPattern(),
            'uniqId',
            function ($matches) {
                $dir = rtrim($matches[1], '/') . '/';
                $uniqId = uniqid();
                $fileName = $uniqId;
                $i = 1;
                while (file_exists($dir . $fileName)) {
                    $fileName = $uniqId . '_' . $i;
                    $i++;
                }

                return $dir . $fileName;
            }
        );
    }

} 