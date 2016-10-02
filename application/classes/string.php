<?php

class String implements ArrayObject 
{
    private $data = "";
    function __construct($value = "")
    {
        $this->data = $value;
    }
    
}
