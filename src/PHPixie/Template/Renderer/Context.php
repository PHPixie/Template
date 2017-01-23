<?php

namespace PHPixie\Template\Renderer;

class Context
{
    /**
     *
     * @var \PHPixie\Template\Extensions 
     */
    protected $extensions;
    
    /**
     *
     * @var \PHPixie\Template\Renderer 
     */
    protected $renderer;
    
    /**
     *
     * @var \PHPixie\Template\Resolver 
     */
    protected $resolver;
    
    protected $template;
    
    /**
     *
     * @var type 
     */
    protected $data;
    
    /**
     *
     * @var \PHPixie\Template\Resolver 
     */
    protected $layout;
    protected $childContent;
    
    protected $variables;
    protected $extensionMethods;
    
    protected $blocks = array();
    protected $blockStack = array();
    
    /**
     * 
     * @param \PHPixie\Template\Extensions $extensions
     * @param \PHPixie\Template\Renderer $renderer
     * @param \PHPixie\Template\Resolver $resolver
     * @param string $template
     * @param \PHPixie\Slice\Type\ArrayData $data
     */
    public function __construct($extensions, $renderer, $resolver, $template, $data)
    {
        $this->extensions = $extensions;
        $this->renderer   = $renderer;
        $this->resolver   = $resolver;
        $this->template   = $template;
        $this->data       = $data;
    }
    
    public function renderer()
    {
        return $this->renderer;
    }
    
    /**
     * 
     * @return \PHPixie\Template\Resolver
     */
    public function resolver()
    {
        return $this->resolver;
    }
    
    public function template()
    {
        return $this->template;
    }
    
    public function data()
    {
        return $this->data;
    }
    
    public function setLayout($name)
    {
        $this->layout = $name;
    }
    
    public function layout()
    {
        return $this->layout;
    }
    
    public function childContent()
    {
        return $this->childContent;
    }

    public function processRender($content)
    {
        $this->assertBlocksClosed();
        
        $this->template     = $this->layout;
        $this->layout       = null;
        $this->childContent = $content;
    }
    
    public function variables()
    {
        return array_merge(
            $this->extensions->aliases(),
            $this->data->get(null, array())
        );
    }

    public function extension($name)
    {
        return $this->extensions->get($name);
    }
    
    public function callExtensionMethod($method, $params)
    {
        $method = $this->extensions->getMethod($method);
        return call_user_func_array($method, $params);
    }
    
    public function blockExists($name)
    {
        return array_key_exists($name, $this->blocks);
    }
    
    public function block($name)
    {
        if($this->blockExists($name)) {
            return $this->blocks[$name];
        }
        
        return null;
    }
    
    /**
     * 
     * @param string $name
     * @param boolean $prepend
     */
    public function startBlock($name, $prepend = false)
    {
        array_push($this->blockStack, array($name, $prepend));
    }
    
    /**
     * 
     * @param string $content
     * @throws \PHPixie\Template\Exception
     */
    public function endBlock($content)
    {
        if(empty($this->blockStack)) {
            throw new \PHPixie\Template\Exception("endBlock() called too many times in '{$this->template}'");
        }
        
        list($name, $prepend) = array_pop($this->blockStack);
        
        if($this->blockExists($name)) {
            if($prepend) {
                $content = $content.$this->blocks[$name];
                
            }else{
                $content = $this->blocks[$name].$content;
            
            }
        }
        
        $this->blocks[$name] = $content;
    }
    
    protected function assertBlocksClosed()
    {
        if(!empty($this->blockStack)) {
            throw new \PHPixie\Template\Exception("Not all open blocks have been closed in '{$this->template}'");
        }
    }
}