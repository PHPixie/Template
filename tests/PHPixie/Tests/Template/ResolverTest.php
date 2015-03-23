<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Resolver
 */
class ResolverTest extends \PHPixie\Test\Testcase
{
    protected $loaders;
    protected $configData;
    
    protected $resolver;
    
    protected $loader;
    protected $overrides = array(
        'pixie' => 'fairy'
    );
    
    public function setUp()
    {
        $this->loaders = $this->quickMock('\PHPixie\Template\Locators');
        $this->configData = $this->getData();
        
        $loaderConfig = $this->getData();
        $this->method($this->configData, 'slice', $loaderConfig, array('loader'), 0);
        
        $this->loader = $this->abstractMock('\PHPixie\Template\Locators\Locator');
        $this->method($this->loaders, 'buildFromConfig', $this->loader, array($loaderConfig), 0);
        
        $this->method($this->configData, 'get', $this->overrides, array('overrides', array()), 1);
        
        $this->resolver = new \PHPixie\Template\Resolver(
            $this->loaders,
            $this->configData
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
     * @covers ::resolve
     * @covers ::<protected>
     */
    public function testResolve()
    {
        $file = 'fairy.php';
        
        $this->method($this->loader, 'getTemplateFile', $file, array('trixie'), 0);
        $this->assertSame($file, $this->resolver->resolve('trixie'));
        $this->assertSame($file, $this->resolver->resolve('trixie'));
        
        $this->method($this->loader, 'getTemplateFile', $file, array('fairy'), 0);
        $this->assertSame($file, $this->resolver->resolve('pixie'));
        $this->assertSame($file, $this->resolver->resolve('pixie'));
        
        $this->method($this->loader, 'getTemplateFile', null, array('blum'), 0);
        $resolver = $this->resolver;
        $this->assertException(function() use($resolver){
            $resolver->resolve('blum');
        }, '\PHPixie\Template\Exception');
    }
    
    protected function getData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
}