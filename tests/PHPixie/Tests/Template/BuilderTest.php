<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    protected $slice;
    protected $filesystemLocator;
    protected $configData;
    protected $filesystemRoot;
    protected $externalExtensions = array();
    protected $externalFormats    = array();
    
    protected $builder;
    
    protected $slices = array();
    
    public function setUp()
    {
        $this->slice             = $this->quickMock('\PHPixie\Slice');
        $this->filesystemLocator = $this->quickMock('\PHPixie\Filesystem\Locators\Locator');
        $this->configData        = $this->getData();
        $this->filesystemRoot    = $this->quickMock('\PHPixie\Filesystem\Root');
        
        for($i=0;$i<2;$i++) {
            $this->externalExtensions[]= $this->abstractMock('\PHPixie\Template\Extensions\Extension');
            $externalFormat = $this->abstractMock('\PHPixie\Template\Formats\Format');
            $this->method($externalFormat, 'handledExtensions', array(), array());
            $this->externalFormats[] = $externalFormat;
        }
        
        foreach(array('extensions', 'compiler', 'resolver') as $name) {
            $this->slices[$name] = $this->getData();
        }
        
        $slices = $this->slices;
        $this->method($this->configData, 'slice', function($name) use($slices) {
            return $slices[$name];
        });
        
        $this->builder = new \PHPixie\Template\Builder(
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
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::extensions
     * @covers ::<protected>
     */
    public function testExtensions()
    {
        $extensions = $this->builder->extensions();
        
        $this->assertInstance($extensions, '\PHPixie\Template\Extensions', array(
            'configData'         =>  $this->slices['extensions'],
            'externalExtensions' =>  $this->externalExtensions
        ));
        
        $this->assertSame($extensions, $this->builder->extensions());
    }
    
    /**
     * @covers ::formats
     * @covers ::<protected>
     */
    public function testFormats()
    {
        $formats = $this->builder->formats();
        
        $this->assertInstance($formats, '\PHPixie\Template\Formats', array(
            
        ));
        
        $this->assertSame($formats, $this->builder->formats());
    }
    
    /**
     * @covers ::compiler
     * @covers ::<protected>
     */
    public function testCompiler()
    {
        $compiler = $this->builder->compiler();
        
        $this->assertInstance($compiler, '\PHPixie\Template\Compiler', array(
            'filesystemRoot' => $this->filesystemRoot,
            'formats'        => $this->builder->formats(),
            'configData'     => $this->slices['compiler']
        ));
        
        $this->assertSame($compiler, $this->builder->compiler());
    }
    
    /**
     * @covers ::resolver
     * @covers ::<protected>
     */
    public function testResolver()
    {
        $resolver = $this->builder->resolver();
        
        $this->assertInstance($resolver, '\PHPixie\Template\Resolver', array(
            'compiler'          => $this->builder->compiler(),
            'filesystemLocator' => $this->filesystemLocator,
        ));
        
        $this->assertSame($resolver, $this->builder->resolver());
    }
    
    /**
     * @covers ::renderer
     * @covers ::<protected>
     */
    public function testRenderer()
    {
        $renderer = $this->builder->renderer();
        
        $this->assertInstance($renderer, '\PHPixie\Template\Renderer', array(
            'slice'           =>  $this->slice,
            'templateBuilder' =>  $this->builder
        ));
        
        $this->assertSame($renderer, $this->builder->renderer());
    }
    
    /**
     * @covers ::container
     * @covers ::<protected>
     */
    public function testContainer()
    {
        $data = array('t' => 1);
        $arrayData = $this->getData();
        $this->method($this->slice, 'editableArrayData', $arrayData, array($data), 0);
        
        $container = $this->builder->container('pixie', $data);
        
        $this->assertInstance($container, '\PHPixie\Template\Container', array(
            'renderer' =>  $this->builder->renderer(),
            'template' =>  'pixie',
            'data'     => $arrayData
        ));
    }

    protected function getData()
    {
        return $this->abstractMock('\PHPixie\Slice\Data');
    }
    
    protected function builderMock($methods = array())
    {
        return $this->getMock('\PHPixie\Template\Builder', $methods, array(
            $this->slice,
            $this->configData,
            $this->externalExtensions,
            $this->externalFormats
        ));
    }
}