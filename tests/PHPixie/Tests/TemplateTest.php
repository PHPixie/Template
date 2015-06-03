<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass \PHPixie\Template
 */
class TemplateTest extends \PHPixie\Test\Testcase
{
    protected $slice;
    protected $filesystemLocator;
    protected $configData;
    protected $filesystemRoot;
    protected $externalExtensions = array();
    protected $externalFormats = array();
    
    protected $template;
    
    public function setUp()
    {
        $this->slice             = $this->quickMock('\PHPixie\Slice');
        $this->filesystemLocator = $this->quickMock('\PHPixie\Filesystem\Locators\Locator');
        $this->configData        = $this->abstractMock('\PHPixie\Slice\Data');
        $this->filesystemRoot    = $this->quickMock('\PHPixie\Filesystem\Locators\Locator');
        
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
            $this->filesystemLocator,
            $this->configData,
            $this->filesystemRoot,
            $this->externalExtensions,
            $this->externalFormats
        ), 0);
        
        $this->template->__construct(
            $this->slice,
            $this->filesystemLocator,
            $this->configData,
            $this->filesystemRoot,
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
            $this->filesystemLocator,
            $this->configData,
            $this->filesystemRoot,
            $this->externalExtensions,
            $this->externalFormats
        );
        
        $this->assertInstance($template->builder(), '\PHPixie\Template\Builder', array(
            'slice'              => $this->slice,
            'filesystemLocator'  => $this->filesystemLocator,
            'configData'         => $this->configData,
            'filesystemRoot'     => $this->filesystemRoot,
            'externalExtensions' => $this->externalExtensions,
            'externalFormats'    => $this->externalFormats
        ));
        
        $template = new \PHPixie\Template(
            $this->slice,
            $this->filesystemLocator,
            $this->configData
        );
        
        $this->assertInstance($template->builder(), '\PHPixie\Template\Builder', array(
            'slice'              => $this->slice,
            'filesystemLocator'  => $this->filesystemLocator,
            'configData'         => $this->configData,
            'filesystemRoot'     => null,
            'externalExtensions' => array(),
            'externalFormats'    => array()
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