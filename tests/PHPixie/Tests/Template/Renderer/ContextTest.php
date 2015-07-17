<?php

namespace PHPixie\Tests\Template\Renderer;

/**
 * @coversDefaultClass \PHPixie\Template\Renderer\Context
 */
class ContextTest extends \PHPixie\Test\Testcase
{
    protected $extensions;
    protected $renderer;
    protected $resolver;
    protected $template;
    protected $data;
    
    protected $context;
    
    public function setUp()
    {
        $this->extensions = $this->quickMock('\PHPixie\Template\Extensions');
        $this->renderer   = $this->quickMock('\PHPixie\Template\Renderer');
        $this->resolver   = $this->quickMock('\PHPixie\Template\Resolver');
        $this->data       = $this->abstractMock('\PHPixie\Slice\Data');
        
        $this->context = new \PHPixie\Template\Renderer\Context(
            $this->extensions,
            $this->renderer,
            $this->resolver,
            $this->template,
            $this->data
        );
    }

    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::template
     * @covers ::<protected>
     */
    public function testTemplate()
    {
        $this->assertSame($this->template, $this->context->template());
    }
    
    /**
     * @covers ::data
     * @covers ::<protected>
     */
    public function testData()
    {
        $this->assertSame($this->data, $this->context->data());
    }
    
    /**
     * @covers ::layout
     * @covers ::setLayout
     * @covers ::<protected>
     */
    public function testLayout()
    {
        $this->assertSame(null, $this->context->layout());
        
        $this->context->setLayout('pixie');
        $this->assertSame('pixie', $this->context->layout());
    }
    
    /**
     * @covers ::renderer
     * @covers ::<protected>
     */
    public function testRenderer()
    {
        $this->assertSame($this->renderer, $this->context->renderer());
    }
    
    /**
     * @covers ::resolver
     * @covers ::<protected>
     */
    public function testResolver()
    {
        $this->assertSame($this->resolver, $this->context->resolver());
    }
    

    /**
     * @covers ::variables
     * @covers ::<protected>
     */
    public function testVariables()
    {
        $aliases = array(
            'pixie'  => 'Fairy',
            'flower' => 'Red'
        );
        
        $data = array(
            'pixie' => 'Trixie',
            'tree'  => 'Oak'
        );
        
        $this->method($this->extensions, 'aliases', $aliases, array(), 0);
        $this->method($this->data, 'get', $data, array(null, array()), 0);
        
        $variables = array_merge($aliases, $data);
        $this->assertSame($variables, $this->context->variables());
    }

    /**
     * @covers ::extension
     * @covers ::<protected>
     */
    public function testExtension()
    {
        $extension = $this->abstractMock('\PHPixie\Template\Extensions\Extension');
        $this->method($this->extensions, 'get', $extension, array('pixie'), 0);
        
        $this->assertSame($extension, $this->context->extension('pixie'));
    }
    
    /**
     * @covers ::callExtensionMethod
     * @covers ::<protected>
     */
    public function testCallExtensionMethod()
    {
        $method = function($a, $b) {
            return $a.$b; 
        };
        
        $this->method($this->extensions, 'getMethod', $method, array('pixie'), 0, true);
        $this->assertSame('FairyTrixie', $this->context->callExtensionMethod('pixie', array('Fairy', 'Trixie')));
    }
    
    /**
     * @covers ::processRender
     * @covers ::childContent
     * @covers ::<protected>
     */
    public function testProcessRender()
    {
        $this->assertSame(null, $this->context->childContent());
        
        $this->context->setLayout('pixie');
        $this->context->processRender('fairy');
        
        $this->assertSame(null, $this->context->layout());
        $this->assertSame('pixie', $this->context->template());
        $this->assertSame('fairy', $this->context->childContent());
        
        $this->context->startBlock('pixie');
        $context = $this->context;
        $this->assertException(function() use($context){
            $context->processRender('fairy');
        }, '\PHPixie\Template\Exception');
    }
    
    /**
     * @covers ::startBlock
     * @covers ::endBlock
     * @covers ::block
     * @covers ::blockExists
     * @covers ::<protected>
     */
    public function testBlocks()
    {
        $this->assertSame(false, $this->context->blockExists('pixie'));
        $this->assertSame(null, $this->context->block('pixie'));
        
        $this->context->startBlock('pixie');
        $this->context->endBlock('Fairy');
        
        $this->context->startBlock('flower');
        $this->context->endBlock('Red');
        
        $this->assertSame(true, $this->context->blockExists('pixie'));
        $this->assertSame('Fairy', $this->context->block('pixie'));
        
        $this->assertSame(true, $this->context->blockExists('flower'));
        $this->assertSame('Red', $this->context->block('flower'));
        
        $this->context->startBlock('pixie');
        $this->context->endBlock('Trixie');
        $this->assertSame('FairyTrixie', $this->context->block('pixie'));
        
        $this->context->startBlock('pixie', true);
        $this->context->endBlock('Blum');
        $this->assertSame('BlumFairyTrixie', $this->context->block('pixie'));        
        
        $context = $this->context;
        $this->assertException(function() use($context){
            $context->endBlock('test');
        }, '\PHPixie\Template\Exception');
    }
}