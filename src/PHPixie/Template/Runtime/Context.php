<?php

namespace PHPixie\Template\Runtime;

class Context
{
    protected $extensions;
    protected $renderer;
    protected $resolver;
    
    protected $layout;
    
    protected $blocks = array();
    protected $blocksStack = array();
    
    public function renderer()
    {
        return $this->renderer;
    }
    
    public function resolver()
    {
        return $this->resolver;
    }
    
    public function file()
    {
        return $this->file;
    }
    
    public function template()
    {
        return $this->template;
    }
    
    public function data()
    {
        return $this->data;
    }
    
    public function variables()
    {
        if($this->variables === null) {
            $this->variables = array_merge(
                $this->extensions->aliases(),
                $this->arrayData->get()
            );
        }
        
        return $this->variables;
    }
    
    public function methods()
    {
        if($this->methods === null) {
            $this->methods = $this->extensions->methods();
        }
        
        return $this->methods;
    }
    
    public function extensionMap()
    {
        if($this->extensionMap === null) {
            $this->extensionMap = $this->extensions->map();
        }
        
        return $this->extensionMap;
    }
    
    
    public function extension($name)
    {
        $extensionMap = $this->extensionMap();
        return $extensionMap[$name];
    }
    
    public function call($method, $params)
    {
        $methods = $this->methods();
        return call_user_func($methods[$method], $params);
    }
    
    public function blockExists($name)
    {
        return array_key_exists($name, $this->blocks);
    }
    
    public function getBlock($name)
    {
        if($this->blockExists($name)) {
            return $this->blocks[$name];
        }
        
        return null;
    }
    
    public function startBlock($name)
    {
        array_push($this->blockStack, $name);
        ob_start();
    }
    
    public function endBlock()
    {
        if(empty($this->blockStack)) {
            throw new \PHPixie\Template\Exception("endBlock() called too many times in '{$this->template}'");
        }
        
        $name  = array_pop($this->blockStack);
        $block = ob_get_clean();
        
        if($this->blockExists($name)) {
            $block = $block.$this->blocks[$name];
        }
        
        $this->blocks[$name] = $block;
    }
    
    public function assertBlocksClosed()
    {
        if(!empty($this->blockStack)) {
            throw new \PHPixie\Template\Exception("Not all open blocks have been closed in '{$this->template}'");
        }
    }
}