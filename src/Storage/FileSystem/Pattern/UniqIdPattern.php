<?php
/**
 * Created by PhpStorm.
 * User: Nikoms
 * Date: 17/09/2014
 * Time: 01:51
 */

namespace Nikoms\FailLover\Storage\FileSystem\Pattern;


class UniqIdPattern  extends RegexPattern implements PatternInterface
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