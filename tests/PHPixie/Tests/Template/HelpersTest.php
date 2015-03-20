<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Helpers
 */
class HelpersTest extends \PHPixie\Test\Testcase
{
    protected $configData;
    protected $externalExtensions = array();
    
    protected $extensions;
    
    protected $extensionNames = array('php');
    
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
     * @covers ::extensions
     * @covers ::methods
     * @covers ::aliases
     * @covers ::<protected>
     */
    public function testMapExtensions()
    {
        $this->mapExtensionsTest(false);
        $this->mapExtensionsTest(true);
    }
    
    protected function mapExtensionsTest($override = false)
    {
        $name = $override ? 'php' : 'haml';
        
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
                $method = $name.'Method'.$i;
                $alias = $name.'Alias'.$i;
                
                $extensionMethods[]=$method;
                $extensionAliases[$alias]=$method;
                
                $methods[$method] = array($extension, $method);
                $aliases[$alias] = array($extension, $method);
            }
            
            $this->method($extension, 'methods', $extensionMethods, array(), 1);
            $this->method($extension, 'aliases', $extensionAliases, array(), 2);
        }
        
        $configAliases = array(
            '_' => array(
                'extension' => 'php',
                'method'    => 'test'
            )
        );
        $aliases['_'] = array($extensions['php'], 'test');
        
        $this->method($this->configData, 'get', $configAliases, array('aliases', array()), 0);
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($extensions, $mock->extensions());
            $this->assertSame($methods, $mock->methods());
            $this->assertEquals($aliases, $mock->aliases());
        }
    }
    
    protected function getExtension()
    {
        return $this->abstractMock('\PHPixie\Template\Helpers\Helper');
    }
    
    protected function extensions()
    {
        return new \PHPixie\Template\Helpers(
            $this->configData,
            $this->externalExtensions
        );
    }
    
    protected function extensionsMock($methods)
    {
        return $this->getMock(
            '\PHPixie\Template\Helpers',
            $methods,
            array(
                $this->configData,
                $this->externalExtensions
            )
        );
    }

}