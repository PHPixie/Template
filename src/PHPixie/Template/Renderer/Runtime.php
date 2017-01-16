<?php

namespace PHPixie\Template\Renderer;

/**
 * @method string httpPath(string $resolverPath, array $attributes=array()) Generate path
 * @method string httpUri(string $resolverPath, array $attributes, boolean $withHost=true) Generate URI
 */
class Runtime
{
    /**
     *
     * @var Context 
     */
    protected $context;
    protected $openBuffers = 0;
    
    public function __construct($context)
    {
        $this->context = $context;
    }
    
    /**
     * 
     * @return string
     */
    public function run()
    {
        extract($this->context->variables());

        try {
            while(($template = $this->context->template()) !== null) {
                ob_start();
                $this->openBuffers++;
                
                include $this->resolve($template);
                
                $content = ob_get_clean();
                $this->openBuffers--;
                
                $this->context->processRender($content);
            }
            
        }catch(\Exception $exception) {
            while($this->openBuffers > 0) {
                ob_end_clean();
                $this->openBuffers--;
            }
            
            throw $exception;
        }
        
        return $content;

    }
    
    /**
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function render($template, $data = array())
    {
        return $this->context->renderer()->render($template, $data);
    }
    
    protected function resolve($template)
    {
        return $this->context->resolver()->resolve($template);
    }
    
    /**
     * 
     * @param string $name
     * @param boolean $onlyIfNotExists
     * @param boolean $prepend
     * @return boolean
     */
    protected function startBlock($name, $onlyIfNotExists = false, $prepend = false)
    {
        if($onlyIfNotExists && $this->context->blockExists($name)) {
            return false;
        }
        
        ob_start();
        $this->openBuffers++;
        
        $this->context->startBlock($name, !$prepend);
        
        return true;
    }
    
    /**
     * 
     * @return null
     */
    protected function endBlock()
    {
        $content = ob_get_clean();
        $this->openBuffers--;
        
        return $this->context->endBlock($content);
    }
    
    /**
     * 
     * @param string $name
     */
    protected function layout($name)
    {
        $this->context->setLayout($name);
    }
    
    protected function childContent()
    {
        echo $this->context->childContent();
    }
    
    protected function blockExists($name)
    {
        return $this->context->blockExists($name);
    }
    
    protected function block($name)
    {
        echo $this->context->block($name);
    }
    
    protected function extension($name)
    {
        return $this->context->extension($name);
    }
    
    public function __call($name, $params)
    {
        return $this->context->callExtensionMethod($name, $params);
    }
    
    protected function get($path, $default = null)
    {
        return $this->data()->get($path, $default);
    }
    
    protected function set($path, $data)
    {
        $this->data()->set($path, $data);
    }
    
    protected function remove($path)
    {
        $this->data()->remove($path);
    }
    
    protected function data()
    {
        return $this->context->data();
    }
}