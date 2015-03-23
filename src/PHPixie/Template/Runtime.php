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
    
    public function __construct($context)
    {
        $this->context = $context;
    }
    
    public function run()
    {
        extract($this->context->variables());
        ob_start();
        
        $exception = null;
        
        try {
            include $this->file;
        }catch(\Exception $caughtException) {
            $exception = $caughtException;
        }
        
        $this->context->assertBlocksClosed();
        
        if($exception !== null) {
            for($i=0; $i<=count($this->blockStack); $i++) {
                ob_end_clean();
            }
            
            throw $exception;
        }
        
        return ob_get_clean();
    }
    
    public function render($name, $data = array())
    {
        return $this->renderer->render($name, $data);
    }
    
    public function resolve($name)
    {
        return $this->resolver->resolve($name);
    }
    
    protected function layout($name)
    {
        $this->layout = $name;
    }
    
    protected function childContent()
    {
        return $this->childContent;
    }
    
    protected function startBlock($name, $onlyIfNotExists = false)
    {
        if($onlyIfNotExists && $this->context->blockExists($name)) {
            return false;
        }
        
        return $this->context->startBlock($name);
        
        return true;
    }
    
    protected function endBlock()
    {
        return $this->context->endBlock($name);
    }
    
    protected function blockExists($name)
    {
        return $this->context->blockExists($name);
    }
    
    protected function block($name)
    {
        return $this->context->block($name);
    }
    
    protected function extension($name)
    {
        return $this->extensionMap[$name];
    }
    
    public function __call($name, $params)
    {
        return $this->context->call($name, $params);
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