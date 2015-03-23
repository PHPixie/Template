<?php

namespace PHPixie\Tests\Template\Locators\Locator;

/**
 * @coversDefaultClass \PHPixie\Template\Locators\Locator\Group
 */
class GroupTest extends \PHPixie\Test\Testcase
{
    protected $locatorBuilder;
    protected $configData;
    
    protected $locator;
    
    protected $locators;
    
    public function setUp()
    {
        $this->configData = $this->getData();
        $this->locatorBuilder = $this->quickMock('\PHPixie\Template\Locators');
        
        $locatorsConfig = $this->getData();
        $this->method($this->configData, 'slice', $locatorsConfig, array('locators'), 0);
        
        $this->method($locatorsConfig, 'keys', array(0, 1), array(null, true), 0);
        
        for($i=0; $i<2; $i++) {
            $locatorConfig = $this->getData();
            $locator = $this->abstractMock('\PHPixie\Template\Locators\Locator');
            
            $this->method($locatorsConfig, 'slice', $locatorConfig, array($i), $i+1);
            $this->method($this->locatorBuilder, 'buildFromConfig', $locator, array($locatorConfig), $i);
            
            $this->locators[] = $locator;
        }
        
        $this->locator = new \PHPixie\Template\Locators\Locator\Group(
            $this->locatorBuilder,
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
        foreach(array('pixie', null) as $path) {
            foreach($this->locators as $key => $locator) {
                $locatorPath = $key == 1 ? $path : null;
                $this->method($locator, 'getTemplateFile', $locatorPath, array('fairy'), 0);
            }
            
            $this->assertSame($path, $this->locator->getTemplateFile('fairy'));
        }
    }
    
    protected function getData()
    {
        return $this->abstractMock('\PHPixie\Slice\Data');
    }
}