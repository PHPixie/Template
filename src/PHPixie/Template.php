<?php

namespace PHPixie;

class Template
{
    protected $builder;
    
    public function __construct($slice, $configData, $extensions = array(), $formats = array())
    {
        $this->builder = $this->buildBuilder($slice, $configData, $extensions, $formats);
    }
    
    public function render($name, $data = array())
    {
        return $this->builder->renderer()->render($name, $data);
    }
    
    public function get($name, $data = array())
    {
        return $this->builder->container($name, $data);
    }
    
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder($slice, $configData, $extension, $formats)
    {
        return new Template\Builder($slice, $configData, $extension, $formats);
    }
}