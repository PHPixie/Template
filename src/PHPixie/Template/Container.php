<?php

namespace PHPixie\Template;

class Container
{
    /**
     *
     * @var Renderer
     */
    protected $renderer;
    protected $template;
    /**
     *
     * @var \PHPixie\Slice\Type\ArrayData\Editable
     */
    protected $data;
    
    public function __construct($renderer, $template, $data)
    {
        $this->renderer = $renderer;
        $this->template = $template;
        $this->data     = $data;
    }
    
    public function template()
    {
        return $this->template;
    }
    
    public function data()
    {
        return $this->data;
    }
    
    public function get($path, $default = null)
    {
        return $this->data->get($path, $default);        
    }
    
    public function set($path, $data)
    {
        $this->data->set($path, $data);
        return $this;
    }
    
    public function remove($path)
    {
        $this->data->remove($path);
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function render()
    {
        return $this->renderer->render(
            $this->template,
            $this->data->get()
        );
    }
    
    public function __set($path, $data)
    {
        $this->set($path, $data);
    }
    
    public function __get($path)
    {
        return $this->get($path);
    }
}
