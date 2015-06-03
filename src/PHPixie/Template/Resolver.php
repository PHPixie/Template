<?php

namespace PHPixie\Template;

class Resolver
{
    protected $compiler;
    protected $filesystemLocator;
    protected $overrides;
    protected $map = array();
    
    public function __construct($compiler, $filesystemLocator, $configData)
    {
        $this->compiler          = $compiler;
        $this->filesystemLocator = $filesystemLocator;
        $this->overrides         = $configData->get('overrides', array());
    }
    
    public function resolve($name)
    {
        if(array_key_exists($name, $this->map)) {
            return $this->map[$name];
        }
        
        if(array_key_exists($name, $this->overrides)) {
            $templateName = $this->overrides[$name];
            
        }else{
            $templateName = $name;
        }
        
        $file = $this->filesystemLocator->locate($templateName);
        if($file === null) {
            throw new \PHPixie\Template\Exception("Template '$name' could not be found");
        }
        
        $file = $this->compiler->compile($file);
        
        $this->map[$name] = $file;
        
        return $file;
    }
}