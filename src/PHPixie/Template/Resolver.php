<?php

namespace PHPixie\Template;

class Resolver
{
    protected $overrides;
    protected $map = array();
    
    public function __construct($resolvers, $configData)
    {
        $this->overrides = $configData->get('overrides', array());
    }
    
    public function resolve($name)
    {
        if(array_key_exists($name, $this->map)) {
            return $this->map[$name];
        }
        
        if(array_key_exists($name, $this->overrides)) {
            $name = $this->overrides[$name];
        }
        
        $this->map[$name] = $file;
        return $file;
    }
}