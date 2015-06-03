<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass \PHPixie\Template
 */
class TemplateTest extends \PHPixie\Test\Testcase
{
    protected $slice;
    protected $filesystemRoot;
    protected $filesystemLocator;
    protected $configData;
    protected $externalExtensions = array();
    protected $externalFormats = array();
    
    protected $template;
    
    public function setUp()
    {
        $this->slice             = $this->quickMock('\PHPixie\Slice');
        $this->filesystemRoot    = $this->quickMock('\PHPixie\Filesystem\Locators\Locator');
        $this->filesystemLocator = $this->quickMock('\PHPixie\Filesystem\Locators\Locator');
        $this->configData        = $this->abstractMock('\PHPixie\Slice\Data');
        
        $externalExtensions = array();
        $externalFormats = array();
        
        for($i=0;$i<2;$i++) {
            $this->externalExtensions[]= $this->abstractMock('\PHPixie\Template\Extensions\Extension');
            $this->externalFormats[] = $this->abstractMock('\PHPixie\Template\Formats\Format');
        }
        
        $this->template = $this->getMockBuilder('\PHPixie\Template')
            ->setMethods(array('buildBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->builder = $this->quickMock('\PHPixie\Template\Builder');
        $this->method($this->template, 'buildBuilder', $this->builder, array(
            $this->slice,
            $this->filesystemRoot,
            $this->filesystemLocator,
            $this->configData,
            $this->externalExtensions,
            $this->externalFormats
        ), 0);
        
        $this->template->__construct(
            $this->slice,
            $this->filesystemRoot,
            $this->filesystemLocator,
            $this->configData,
            $this->externalExtensions,
            $this->externalFormats
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructor()
    {
        
    }
    
    /**
     * @covers ::buildBuilder
     * @covers ::<protected>
     */
    public function testBuildBuilder()
    {
        $template = new \PHPixie\Template(
            $this->slice,
            $this->filesystemRoot,
            $this->filesystemLocator,
            $this->configData,
            $this->externalExtensions,
            $this->externalFormats
        );
        
        $builder = $template->builder();
        
        $this->assertInstance($builder, '\PHPixie\Template\Builder', array(
            'slice'              => $this->slice,
            'filesystemRoot'     => $this->filesystemRoot,
            'filesystemLocator'  => $this->filesystemLocator,
            'configData'         => $this->configData,
            'externalExtensions' => $this->externalExtensions,
            'externalFormats'    => $this->externalFormats
        ));
    }
    
    /**
     * @covers ::render
     * @covers ::<protected>
     */
    public function testRender()
    {
        $renderer = $this->quickMock('\PHPixie\Template\Renderer');
        
        $this->method($this->builder, 'renderer', $renderer, array(), 0);
        $this->method($renderer, 'render', 'test', array('pixie', array()), 0);
        $this->assertSame('test', $this->template->render('pixie'));
        
        $data = array('t' => 1);
        $this->method($this->builder, 'renderer', $renderer, array(), 0);
        $this->method($renderer, 'render', 'test', array('pixie', $data), 0);
        $this->assertSame('test', $this->template->render('pixie', $data));
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        $container = $this->quickMock('\PHPixie\Template\Container');
        
        $this->method($this->builder, 'container', $container, array('pixie', array()), 0);
        $this->assertSame($container, $this->template->get('pixie'));
        
        $data = array('t' => 1);
        $this->method($this->builder, 'container', $container, array('pixie', $data), 0);
        $this->assertSame($container, $this->template->get('pixie', $data));
    }
    
    /**
     * @covers ::builder
     * @covers ::<protected>
     */
    public function testBuilder()
    {
        $this->assertSame($this->builder, $this->template->builder());
    }
}