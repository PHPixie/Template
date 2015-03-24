<?php

namespace PHPixie\Template;

class Renderer
{
    protected $templateBuilder;
    protected $slice;
    protected $resolver;
    
    public function __construct($templateBuilder, $slice)
    {
        $this->templateBuilder = $templateBuilder;
        $this->slice = $slice;
    }
    
    public function render($name, $data) {
        $arrayData = $this->slice->arrayData($data);
        $context   = $this->context($name, $arrayData);
        $runtime   = $this->runtime($context);
        return $runtime->run();
    }
    
    public function runtime($context)
    {
        return new Renderer\Runtime($context);
    }
    
    public function context($template, $arrayData)
    {
        return new Renderer\Context(
            $this->templateBuilder->extensions(),
            $this,
            $this->templateBuilder->resolver(),
            $template,
            $arrayData
        );
    }
}