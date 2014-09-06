<?php


namespace Nikoms\FailLover\Filter;


class EmptyFilter implements FilterInterface{

    public function __toString()
    {
        //We will never have a class named "Class" with a method name "function" :)
        return 'EmptyFilter\Class::function';
    }

} 