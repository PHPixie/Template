<?php

namespace PHPixie;

class Template
{
    protected $builder;
    
    public function __construct(
        $slice,
        $filesystemLocator,
        $configData,
        $filesystemRoot     = null,
        $externalExtensions = array(),
        $externalFormats    = array()
    )
    {
        $this->builder = $this->buildBuilder(
            $slice,
            $filesystemLocator,
            $configData,
            $filesystemRoot,
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
        $filesystemLocator,
        $configData,
        $filesystemRoot,
        $externalExtensions,
        $externalFormats
    )
    {
        return new Template\Builder(
            $slice,
            $filesystemLocator,
            $configData,
            $filesystemRoot,
            $externalExtensions,
            $externalFormats
        );
    }
}