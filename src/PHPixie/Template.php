<?php

namespace PHPixie;

class Template
{
    protected $builder;
    
    public function __construct(
        $slice,
        $filesystemRoot,
        $filesystemLocator,
        $configData,
        $externalExtensions = array(),
        $externalFormats    = array()
    )
    {
        $this->builder = $this->buildBuilder(
            $slice,
            $filesystemRoot,
            $filesystemLocator,
            $configData,
            $externalExtensions,
            $externalFormats
        );
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
    
    protected function buildBuilder(
        $slice,
        $filesystemRoot,
        $filesystemLocator,
        $configData,
        $externalExtensions,
        $externalFormats
    )
    {
        return new Template\Builder(
            $slice,
            $filesystemRoot,
            $filesystemLocator,
            $configData,
            $externalExtensions,
            $externalFormats
        );
    }
}