<?php


namespace Nikoms\FailLover\TestCaseResult;


interface TestCaseInterface {

    public function getClassName();
    public function getName();
    public function getData();
    public function getDataName();
} 