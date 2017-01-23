<?php

namespace PHPixie\Template;

class Extensions
{
    protected $configData;
    protected $externalExtensions;
    
    protected $extensions;
    protected $methods;
    protected $aliases;
    
    protected $extensionNames = array('html', 'format');
    protected $classMap = array(
        'html' => '\PHPixie\Template\Extensions\Extension\HTML',
        'format' => '\PHPixie\Template\Extensions\Extension\Format'
    );
    
    public function __construct($configData, $externalExtensions)
    {
        $this->configData = $configData;
        $this->externalExtensions = $externalExtensions;
    }

    public function get($name)
    {
        $this->requireMappedExtensions();
        return $this->extensions[$name];
    }
    
    /**
     * 
     * @param string $name
     * @return type
     */
    public function getMethod($name)
    {
        $this->requireMappedExtensions();
         return $this->methods[$name];
    }
    
    public function aliases()
    {
        $this->requireMappedExtensions();
        return $this->aliases;
    }
    
    protected function requireMappedExtensions()
    {
        if($this->extensions === null) {
            $this->mapExtensions();
        }
    }
    
    protected function mapExtensions()
    {
        $extensions = array();
        
        foreach($this->extensionNames as $name) {
            $extension = $this->buildExtension($name);
            $extensions[$extension->name()] = $extension;
        }
        
        foreach($this->externalExtensions as $extension) {
            $extensions[$extension->name()] = $extension;
        }
        
        $methods = array();
        $aliases = array();
        foreach($extensions as $name => $extension) {
            
            foreach($extension->methods() as $methodAlias => $method) {
                $methods[$methodAlias] = array($extension, $method);
            }
            
            foreach($extension->aliases() as $alias => $method) {
                $aliases[$alias] = array($extension, $method);
            }
        }
        
        $configAliases = $this->configData->get('aliases', array());
        
        foreach($configAliases as $alias => $config) {
            $aliases[$alias] = array(
                $extensions[$config['extension']],
                $config['method']
            );
        }
        
        $this->extensions = $extensions;
        $this->methods    = $methods;
        $this->aliases    = $aliases;
    }
    
    protected function buildExtension($name)
    {
        $class = $this->classMap[$name];
        return new $class();
    }
}