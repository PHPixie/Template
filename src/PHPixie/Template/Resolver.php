<?php

namespace PHPixie\Template;

class Resolver
{
    protected $compiler;
    protected $locator;
    protected $overrides;
    protected $map = array();
    
    public function __construct($filesystem, $compiler, $configData)
    {
        $this->compiler  = $compiler;
        $locatorConfig   = $configData->slice('locator');
        $this->locator   = $filesystem->locator($locatorConfig);
        $this->overrides = $configData->get('overrides', array());
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
        
        $file = $this->locator->locate($templateName);
        if($file === null) {
            throw new \PHPixie\Template\Exception("Template '$name' could not be found");
        }
        
        $file = $this->compiler->compile($file);
        
        $this->map[$name] = $file;
        
        return $file;
    }
}