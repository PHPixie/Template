<?php

namespace PHPixie\Template;

abstract class Data
{
    protected $dataArray;
    
    public function __construct($dataArray)
    {
        $this->dataArray = $dataArray;
    }
    
    public function get($path, $default = null)
    {
        return $this->arrayData->get($path, $default);
    }
    
    public function set($path, $data)
    {
        $this->arrayData->set($path, $data);
    }
    
    public function remove($path, $data)
    {
        $this->arrayData->remove($path, $data);
    }
    
    public function data()
    {
        return $this->dataArray;
    }
}