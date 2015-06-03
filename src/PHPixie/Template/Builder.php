<?php

namespace PHPixie\Template;

class Builder
{
    protected $slice;
    protected $filesystemLocator;
    protected $configData;
    protected $filesystemRoot;
    protected $externalExtensions;
    protected $externalFormats;
    
    protected $instances = array();
    
    public function __construct(
        $slice,
        $filesystemLocator,
        $configData,
        $filesystemRoot,
        $externalExtensions,
        $externalFormats
    )
    {
        $this->slice              = $slice;
        $this->filesystemLocator  = $filesystemLocator;
        $this->configData         = $configData;
        $this->filesystemRoot     = $filesystemRoot;
        $this->externalExtensions = $externalExtensions;
        $this->externalFormats    = $externalFormats;
    }
    
    public function extensions()
    {
        return $this->instance('extensions');
    }
    
    public function formats()
    {
        return $this->instance('formats');
    }
    
    public function compiler()
    {
        return $this->instance('compiler');
    }
    
    public function renderer()
    {
        return $this->instance('renderer');
    }
    
    public function resolver()
    {
        return $this->instance('resolver');
    }
    
    public function container($name, $data = array())
    {
        $data = $this->slice->editableArrayData($data);
        return new Container(
            $this->renderer(),
            $name,
            $data
        );
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildExtensions()
    {
        return new Extensions(
            $this->configData->slice('extensions'),
            $this->externalExtensions
        );
    }
    
    protected function buildFormats()
    {
        return new Formats($this->externalFormats);
    }
    
    protected function buildCompiler()
    {
        return new Compiler(
            $this->filesystemRoot,
            $this->formats(),
            $this->configData->slice('compiler')
        );
    }
    
    protected function buildResolver()
    {
        return new Resolver(
            $this->compiler(),
            $this->filesystemLocator,
            $this->configData->slice('resolver')
        );
    }
    
    protected function buildRenderer()
    {
        return new Renderer(
            $this,
            $this->slice
        );
    }

}