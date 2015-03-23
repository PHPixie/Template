<?php

namespace PHPixie\Tests\Template\Locators\Locator;

/**
 * @coversDefaultClass \PHPixie\Template\Locators\Locator\Prefix
 */
class PrefixTest extends \PHPixie\Test\Testcase
{
    protected $resolverBuilder;
    protected $configData;
    
    protected $resolver;
    
    protected $defaultPrefix = 'default';
    protected $resolvers;
    
    public function setUp()
    {
        $this->configData = $this->getData();
        $this->resolverBuilder = $this->quickMock('\PHPixie\Template\Locators');
        
        $this->method($this->configData, 'get', $this->defaultPrefix, array('defaultPrefix', 'default'), 0);
        
        $resolversConfig = $this->getData();
        $this->method($this->configData, 'slice', $resolversConfig, array('resolvers'), 1);
        
        $this->resolvers = array(
            'default' => $this->abstractMock('\PHPixie\Template\Locators\Locator'),
            'second'  => $this->abstractMock('\PHPixie\Template\Locators\Locator'),
        );
        $this->method($resolversConfig, 'keys', array_keys($this->resolvers), array(null, true), 0);
        
        $i=0;
        
        foreach($this->resolvers as $key => $resolver) {
            $resolverConfig = $this->getData();
            
            $this->method($resolversConfig, 'slice', $resolverConfig, array($key), $i+1);
            $this->method($this->resolverBuilder, 'buildFromConfig', $resolver, array($resolverConfig), $i);
            
            $i++;
        }
        
        $this->resolver = new \PHPixie\Template\Locators\Locator\Prefix(
            $this->resolverBuilder,
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
     * @covers ::getTemplateFile
     * @covers ::<protected>
     */
    public function testGetTemplateFile()
    {
        $this->method($this->resolvers['second'], 'getTemplateFile', 'pixie', array('fairy'), 0);
        $this->assertSame('pixie', $this->resolver->getTemplateFile('second:fairy'));
        
        $this->method($this->resolvers['default'], 'getTemplateFile', 'pixie', array('fairy'), 0);
        $this->assertSame('pixie', $this->resolver->getTemplateFile('fairy'));
    }
    
    protected function getData()
    {
        return $this->abstractMock('\PHPixie\Slice\Data');
    }
}