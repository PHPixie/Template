<?php

namespace PHPixie\Template;

class Runtime
{
    protected $extensions;
    protected $file;
    protected $arrayData;
    protected $childContent;
    protected $blocks;
    
    protected $extensionMap;
    protected $methods;
    
    protected $layout;
    protected $blockStack = array();
    
    public function __construct($extensions, $file, $arrayData, $childContent = null, $blocks = array())
    {
        $this->extensions   = $extensions;
        $this->file         = $file;
        $this->arrayData    = $arrayData;
        $this->childContent = $childContent;
        $this->blocks       = $blocks;
    }
    
    public function run()
    {
        $this->extensionMap = $this->extensions->map();
        $this->methods      = $this->extensions->methods();
        
        extract($this->extensions->aliases());
        extract($this->arrayData->get());
        ob_start();
        
        $exception = null;
        
        try {
            include $this->file;
        }catch(\Exception $caughtException) {
            $exception = $caughtException;
        }
        
        if(!empty($this->blockStack)) {
            $exception = new \PHPixie\Template\Exception("Not all template blocks have been closed in '{$this->file}'");
        }
        
        if($exception !== null) {
            for($i=0; $i<=count($this->blockStack); $i++) {
                ob_end_clean();
            }
            
            throw $exception;
        }
        
        return ob_get_clean();
    }
    
    protected function layout($name)
    {
        $this->layout = $name;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    protected function childContent()
    {
        return $this->childContent;
    }
    
    protected function startBlock($name, $onlyIfNotExists = false)
    {
        if($onlyIfNotExists && $this->blockExists($name)) {
            return false;
        }
        
        array_push($this->blockStack, $name);
        ob_start();
        
        return true;
    }
    
    protected function endBlock()
    {
        if(empty($this->blockStack)) {
            throw new \PHPixie\Template\Exception("endBlock() called too many times in '{$this->file}'");
        }
        
        $name  = array_pop($this->blockStack);
        $block = ob_get_clean();
        
        
        if($this->blockExists($name)) {
            $block = $block.$this->blocks[$name];
        }
        
        $this->blocks[$name] = $block;
    }
    
    protected function blockExists($name)
    {
        return array_key_exists($name, $this->blocks);
    }
    
    protected function block($name)
    {
        if($this->blockExists($name)) {
            return $this->blocks[$name];
        }
        
        return null;
    }
    
    protected function extension($name)
    {
        return $this->extensionMap[$name];
    }
    
    public function __call($name, $params)
    {
        return call_user_func_array($this->methods[$name], $params);
    }
    
    public function getBlocks()
    {
        return $this->blocks;
    }
    
    protected function get($path, $default = null)
    {
        return $this->arrayData->get($path, $default);
    }
    
    protected function set($path, $data)
    {
        $this->arrayData->set($path, $data);
    }
    
    protected function remove($path)
    {
        $this->arrayData->remove($path);
    }
    
    public function data()
    {
        return $this->arrayData;
    }
}