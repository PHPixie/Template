<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Extensions
 */
class ExtensionsTest extends \PHPixie\Test\Testcase
{
    protected $configData;
    protected $externalExtensions = array();
    
    protected $extensions;
    
    protected $extensionNames = array('html', 'format');
    
    public function setUp()
    {
        $this->configData = $this->abstractMock('\PHPixie\Slice\Data');
    }
    
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->extensions();
    }
    
    /**
     * @covers ::get
     * @covers ::getMethod
     * @covers ::aliases
     * @covers ::<protected>
     */
    public function testMapExtensions()
    {
        $this->mapExtensionsTest(false);
        $this->mapExtensionsTest(true);
    }
    
    /**
     * @covers ::<protected>
     */
    public function testBuildExtension()
    {
        $this->method($this->configData, 'get', array(), array('aliases', array()), 0);
        $html = $this->extensions()->get('html');
        
        $this->assertInstance($html, '\PHPixie\Template\Extensions\Extension\HTML');
    }
    
    protected function mapExtensionsTest($override = false)
    {
        $name = $override ? 'html' : 'haml';
        
        $this->externalExtensions = array(
            $name     => $this->getExtension(),
            'fairies' => $this->getExtension()
        );
        
        $mock = $this->extensionsMock(array('buildExtension'));
        
        $extensions = array();
        
        foreach($this->extensionNames as $key => $name) {
            $extension = $this->getExtension();
            $this->method($mock, 'buildExtension', $extension, array($name), $key);
            $this->method($extension, 'name', $name, array(), 0);
            $extensions[$name] = $extension;
        }
        
        foreach($this->externalExtensions as $name => $extension) {
            $this->method($extension, 'name', $name, array(), 0);
        }
        
        $extensions = array_merge($extensions, $this->externalExtensions);
        
        $methods = array();
        $aliases = array();
        
        foreach($extensions as $name => $extension) {
            
            $extensionMethods = array();
            $extensionAliases = array();
            
            for($i=0; $i<2; $i++) {
                $method      = $name.'Method'.$i;
                $methodAlias = 'alias'.$method;
                $alias       = $name.'Alias'.$i;
                
                
                $extensionMethods[$methodAlias]=$method;
                $extensionAliases[$alias]=$method;
                
                $methods[$methodAlias] = array($extension, $method);
                $aliases[$alias]       = array($extension, $method);
            }
            
            $this->method($extension, 'methods', $extensionMethods, array(), 1);
            $this->method($extension, 'aliases', $extensionAliases, array(), 2);
        }
        
        $configAliases = array(
            '_' => array(
                'extension' => 'html',
                'method'    => 'test'
            )
        );
        $aliases['_'] = array($extensions['html'], 'test');
        
        $this->method($this->configData, 'get', $configAliases, array('aliases', array()), 0);
        
        foreach($extensions as $name => $extension) {
            $this->assertSame($extension, $mock->get($name));
        }
        
        foreach($methods as $name => $method) {
            $this->assertSame($method, $mock->getMethod($name));
        }
        
        $this->assertEquals($aliases, $mock->aliases());
    }
    
    protected function getExtension()
    {
        return $this->abstractMock('\PHPixie\Template\Extensions\Extension');
    }
    
    protected function extensions()
    {
        return new \PHPixie\Template\Extensions(
            $this->configData,
            $this->externalExtensions
        );
    }
    
    protected function extensionsMock($methods)
    {
        return $this->getMock(
            '\PHPixie\Template\Extensions',
            $methods,
            array(
                $this->configData,
                $this->externalExtensions
            )
        );
    }

}