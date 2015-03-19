<?php

namespace PHPixie\Template;

class Helpers
{
    protected $configData;
    protected $aliases;

    public function variableAliases()
    {
        if($this->aliases === null) {
            $this->aliases = $this->buildAliases();
        }
        
        return $this->aliases;
    }
    
    public function methodAliases()
    {
        if($this->aliases === null) {
            $this->aliases = $this->buildAliases();
        }
        
        return $this->aliases;
    }
    
    protected function buildVariableAliases()
    {
        $aliases = array();
        $keys = $this->configData->keys();
        
        foreach($keys as $key) {
            $slice = $this->configData->slice($key);
            
            $helper = $this->get($slice->getRequired('helper'));
            $method = $this->get($slice->getRequired('method'));
            
            $aliases[$key] = $helper->$method;
        }
        
        if(array_key_exists('_', $aliases)) {
            $aliases['_'] = array($this->get('html'), 'output');
        }
    }
}