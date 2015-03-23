<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Locators
 */
class LocatorsTest extends \PHPixie\Test\Testcase
{
    protected $locators;
    
    public function setUp()
    {
        $this->locators = new \PHPixie\Template\Locators();
    }
    
    /**
     * @covers ::directory
     * @covers ::<protected>
     */
    public function testDirectory()
    {
        $configData = $this->getTypeConfig('directory');
        
        $locator = $this->locators->directory($configData);
        $this->assertInstance($locator, '\PHPixie\Template\Locators\Locator\Directory', array(
            'directory' => '/fairy',
            'defaultExtension' => 'php'
        ));
    }
    
    /**
     * @covers ::group
     * @covers ::<protected>
     */
    public function testGroup()
    {
        $configData = $this->getTypeConfig('group');
        
        $locator = $this->locators->group($configData);
        $this->assertInstance($locator, '\PHPixie\Template\Locators\Locator\Group', array(
            'locators' => array()
        ));
    }
    
    /**
     * @covers ::prefix
     * @covers ::<protected>
     */
    public function testPrefix()
    {
        $configData = $this->getTypeConfig('prefix');
        
        $locator = $this->locators->prefix($configData);
        $this->assertInstance($locator, '\PHPixie\Template\Locators\Locator\Prefix', array(
            'locators' => array()
        ));
    }
    
    /**
     * @covers ::buildFromConfig
     * @covers ::<protected>
     */
    public function testBuildFromConfig()
    {
        foreach(array('directory', 'group', 'prefix') as $type) {
            $configData = $this->getTypeConfig($type);
            $locator = $this->locators->buildFromConfig($configData);
            $this->assertInstance($locator, '\PHPixie\Template\Locators\Locator\\'.ucfirst($type));
        }
        
        $locators = $this->locators;
        $configData = $this->getTypeConfig('pixie');
        
        $this->assertException(function() use($locators, $configData){
            $locators->buildFromConfig($configData);
        }, '\PHPixie\Template\Exception');
    }
    
    protected function getTypeConfig($type)
    {
        if($type === 'directory') {
            return $this->getConfigData(array(
                'directory' => '/fairy',
                'defaultExtension' => 'php',
                'type' => 'directory'
            ));
        }
        
        if($type === 'group') {
            $locatorsConfig = $this->getConfigData();
            return $this->getConfigData(array(
                'type' => 'group'
            ), $locatorsConfig);
        }
        
        if($type === 'prefix') {
            $locatorsConfig = $this->getConfigData();
            return $this->getConfigData(array(
                'defaultPrefix' => 'default',
                'type' => 'prefix'
            ), $locatorsConfig);
        }
        
        return $this->getConfigData(array(
            'type' => $type
        ));
    }
    
    protected function getConfigData($data = array(), $slice = null, $keys = array())
    {
        $get = function($key) use($data) {
            return $data[$key];
        };
        
        $configData = $this->getData();
        $this->method($configData, 'get', $get);
        $this->method($configData, 'getRequired', $get);
        $this->method($configData, 'slice', $slice);
        $this->method($configData, 'keys', $keys);
        
        return $configData;
    }
    
    protected function getData()
    {
        return $this->abstractMock('\PHPixie\Slice\Data');
    }
}