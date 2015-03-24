<?php

namespace PHPixie\Template;

class Resolver
{
    protected $compiler;
    protected $loader;
    protected $overrides;
    protected $map = array();
    
    public function __construct($loaders, $compiler, $configData)
    {
        $this->compiler = $compiler;
        $loaderConfig = $configData->slice('loader');
        $this->loader = $loaders->buildFromConfig($loaderConfig);
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
        
        $file = $this->loader->getTemplateFile($templateName);
        if($file === null) {
            throw new \PHPixie\Template\Exception("Template '$name' could not be found");
        }
        
        $file = $this->compiler->compile($file);
        
        $this->map[$name] = $file;
        
        return $file;
    }
}