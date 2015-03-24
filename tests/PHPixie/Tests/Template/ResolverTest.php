<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Resolver
 */
class ResolverTest extends \PHPixie\Test\Testcase
{
    protected $loaders;
    protected $compiler;
    protected $configData;
    
    protected $resolver;
    
    protected $loader;
    protected $overrides = array(
        'pixie' => 'fairy'
    );
    
    public function setUp()
    {
        $this->loaders  = $this->quickMock('\PHPixie\Template\Locators');
        $this->compiler = $this->quickMock('\PHPixie\Template\Compiler');
        $this->configData = $this->getData();
        
        $loaderConfig = $this->getData();
        $this->method($this->configData, 'slice', $loaderConfig, array('loader'), 0);
        
        $this->loader = $this->abstractMock('\PHPixie\Template\Locators\Locator');
        $this->method($this->loaders, 'buildFromConfig', $this->loader, array($loaderConfig), 0);
        
        $this->method($this->configData, 'get', $this->overrides, array('overrides', array()), 1);
        
        $this->resolver = new \PHPixie\Template\Resolver(
            $this->loaders,
            $this->compiler,
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
        $compiledFile = 'trixie.php';
        
        $templateMap = array(
            'trixie' => 'trixie',
            'pixie'  => 'fairy'
        );
        
        foreach($templateMap as $template => $override) {
            $this->method($this->loader, 'getTemplateFile', $file, array($override), 0);
            $this->method($this->compiler, 'compile', $compiledFile, array($file), 0);
            
            $this->assertSame($compiledFile, $this->resolver->resolve($template));
            $this->assertSame($compiledFile, $this->resolver->resolve($template));
        }
        
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