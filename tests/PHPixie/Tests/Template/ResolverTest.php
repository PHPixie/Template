<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Resolver
 */
class ResolverTest extends \PHPixie\Test\Testcase
{
    protected $compiler;
    protected $filesystemLocator;
    protected $configData;
    
    protected $resolver;
    
    protected $overrides = array(
        'pixie' => 'fairy'
    );
    
    public function setUp()
    {
        $this->compiler          = $this->quickMock('\PHPixie\Template\Compiler');
        $this->filesystemLocator = $this->abstractMock('\PHPixie\Filesystem\Locators\Locator');
        $this->configData        = $this->getData();
        
        $this->method($this->configData, 'get', $this->overrides, array('overrides', array()), 0);
        
        $this->resolver = new \PHPixie\Template\Resolver(
            $this->compiler,
            $this->filesystemLocator,
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
            $this->method($this->filesystemLocator, 'locate', $file, array($override), 0);
            $this->method($this->compiler, 'compile', $compiledFile, array($file), 0);
            
            $this->assertSame($compiledFile, $this->resolver->resolve($template));
            $this->assertSame($compiledFile, $this->resolver->resolve($template));
        }
        
        $this->method($this->filesystemLocator, 'locate', null, array('blum'), 0);
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