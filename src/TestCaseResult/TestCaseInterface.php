<?php


namespace Nikoms\FailLover\TestCaseResult;


interface TestCaseInterface {
    /**
     * @param string $separator
     * @return string
     */
    public function getFilter($separator = '/');


    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return mixed|null
     */
    public function getData();

    /**
     * @return null|string
     */
    public function getDataName();

    /**
     * @return string
     */
    public function getMethod();
} 