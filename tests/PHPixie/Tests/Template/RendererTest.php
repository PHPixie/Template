<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Renderer
 */
class RendererTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $slice;
    
    protected $renderer;
    
    protected $dependencies;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Template\Builder');
        $this->slice   = $this->quickMock('\PHPixie\Slice');
        
        $this->renderer = new \PHPixie\Template\Renderer(
            $this->builder,
            $this->slice
        );
        
        $dependencies = array('extensions', 'resolver');
        foreach($dependencies as $name) {
            $instance = $this->quickMock('\PHPixie\Template\\'.ucfirst($name));
            $this->method($this->builder, $name, $instance, array());
            $this->dependencies[$name] = $instance;
        }
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::runtime
     * @covers ::<protected>
     */
    public function testRuntime()
    {
        $context = $this->getContext();
        $runtime = $this->renderer->runtime($context);
        
        $this->assertInstance($runtime, '\PHPixie\Template\Renderer\Runtime', array(
            'context' => $context
        ));
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $template = 'pixie';
        $data = $this->getData();
        
        $context = $this->renderer->context($template, $data);
        
        $this->assertInstance($context, '\PHPixie\Template\Renderer\Context', array(
            'extensions' => $this->dependencies['extensions'],
            'renderer'   => $this->renderer,
            'resolver'   => $this->dependencies['resolver'],
            'template'   => $template,
            'data'       => $data
        ));
    }
    
    /**
     * @covers ::render
     * @covers ::<protected>
     */
    public function testRender()
    {
        $mock = $this->getMock('\PHPixie\Template\Renderer', array(
            'context',
            'runtime'
        ), array(
            $this->builder,
            $this->slice
        ));
        
        $template = 'pixie';
        $array = array('t' => 1);
        
        $arrayData = $this->getData();
        $this->method($this->slice, 'editableArrayData', $arrayData, array($array));
        
        $context = $this->getContext();
        $this->method($mock, 'context', $context, array($template, $arrayData), 0);
        
        $runtime = $this->getRuntime();
        $this->method($mock, 'runtime', $runtime, array($context), 1);
        
        $this->method($runtime, 'run', 'result', array(), 0);
        $this->assertSame('result', $mock->render($template, $array));
    }
    
    protected function getContext()
    {
        return $this->quickMock('\PHPixie\Template\Renderer\Context');
    }
    
    protected function getData()
    {
        return $this->abstractMock('\PHPixie\Slice\Data');
    }
    
    protected function getRuntime()
    {
        return $this->abstractMock('\PHPixie\Template\Renderer\Runtime');
    }
}