<?php

namespace PHPixie\Template;

class Renderer
{
    protected $slice;
    
    protected function __construct($slice)
    {
        $this->slice = $slice;
    }
    
    public function render($name, $arrayData) {
        $source   = $this->resolver->resolve($name);
        $compiled = $this->compiler->compile($source);
        $runtime  = $this->builder->runtime(
            $this->extensions,
            $source,
            $arrayData
        );
        
        
        $content = $runtime->run();
        if($content->getLayout()
    }
}