<?php

namespace PHPixie;

class Template
{
    protected $builder;
    
    public function __construct($slice, $extensions = array(), $formats = array())
    {
        $this->builder = $this->buildBuilder($slice, $extensions = array(), $formats = array());
    }
    
    public function render($name, $data = array())
    {
        return $this->builder->renderer()->render($name, $data = array());
    }
    
    public function directory($directory, $name, $defaultFormat = 'php')
    {
        return $this->builder->storages()->directory(
            $directory,
            $name,
            $defaultFormat
        );
    }
    
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder($slice, $extension, $formats)
    {
        return new Template\Builder($slice, $extension, $formats);
    }
}