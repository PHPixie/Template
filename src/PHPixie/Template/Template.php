<?php

namespace PHPixie\Template;

class Template
{
    protected $arrayData;
    
    public function __construct($arrayData)
    {
        $this->arrayData = $arrayData;
    }
    
    public function get($path, $default = null)
    {
        return $this->arrayData->get($path, $default);
    }
    
    public function set($path, $data)
    {
        $this->arrayData->set($path, $data);
    }
    
    public function remove($path)
    {
        $this->arrayData->remove($path);
    }
    
    public function data()
    {
        return $this->arrayData;
    }
}