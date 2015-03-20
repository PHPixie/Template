<?php

namespace PHPixie\Template;

class Container
{
    protected $renderer;
    protected $templateName;
    protected $arrayData;
    
    public function __construct($renderer, $templateName, $arrayData)
    {
        $this->renderer     = $renderer;
        $this->templateName = $templateName;
        $this->arrayData    = $arrayData;
    }
    
    public function templateName()
    {
        return $this->templateName;
    }
    
    public function data()
    {
        return $this->arrayData;
    }
    
    public function get($path, $default = null)
    {
        return $this->arrayData->get($path, $default);
        return $this;
    }
    
    public function set($path, $data)
    {
        $this->arrayData->set($path, $data);
        return $this;
    }
    
    public function remove($path)
    {
        $this->arrayData->remove($path);
        return $this;
    }

    public function render()
    {
        return $this->renderer->render($this->templateName, $this->arrayData);
    }
}