<?php

namespace PHPixie\Templating;

class Runtime
{
    protected $file;
    protected $arrayData;
    protected $layout;
    
    protected $blocks;
    protected $blockStack;
    
    public function __construct($file, $arrayData)
    {
        
    }
    
    public function run($file, $arrayData)
    {
        $this->reset();
        
        extract($this->arrayData->data());
        ob_start();
        include $this->file;
        $content = ob_get_clean();
        
        if(!empty($this->blockStack))
            throw new \Exception();
        
        return 4;
    }
    
    protected function layout($name)
    {
        $this->layout = $name;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function startBlock($name)
    {
        array_push($this->blocks, $name);
        ob_start();
    }
    
    public function endBlock()
    {
        $name = array_pop($this->blockStack);
        $this->blocks[$name] = ob_get_clean();
    }
    
    public function getBlocks()
    {
        return $this->blocks;
    }
}