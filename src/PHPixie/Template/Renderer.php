<?php

namespace PHPixie\Template;

class Renderer
{
    /**
     *
     * @var Builder
     */
    protected $templateBuilder;
    /**
     *
     * @var \PHPixie\Slice
     */
    protected $slice;
    protected $resolver;
    
    /**
     * 
     * @param Builder $templateBuilder
     * @param \PHPixie\Slice $slice
     */
    public function __construct($templateBuilder, $slice)
    {
        $this->templateBuilder = $templateBuilder;
        $this->slice = $slice;
    }
    
    /**
     * 
     * @param string $name
     * @param array|string|null $data
     * @return string
     */
    public function render($name, $data) {
        $arrayData = $this->slice->editableArrayData($data);
        $context   = $this->context($name, $arrayData);
        $runtime   = $this->runtime($context);
        return $runtime->run();
    }
    
    /**
     * 
     * @param \PHPixie\Template\Renderer\Context $context
     * @return \PHPixie\Template\Renderer\Runtime
     */
    public function runtime($context)
    {
        return new Renderer\Runtime($context);
    }
    
    /**
     * 
     * @param string $template
     * @param \PHPixie\Slice\Type\ArrayData $arrayData
     * @return \PHPixie\Template\Renderer\Context
     */
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
