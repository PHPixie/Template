<?php

namespace PHPixie\Template\Locators\Locator;

class Prefix implements \PHPixie\Template\Locators\Locator
{
    protected $locators = array();
    protected $defaultPrefix;
    
    public function __construct($locators, $configData)
    {
        $this->defaultPrefix = $configData->get('defaultPrefix', 'default');
        $locatorsConfig = $configData->slice('locators');
        
        foreach($locatorsConfig->keys(null, true) as $key) {
            $locatorConfig = $locatorsConfig->slice($key);
            $this->locators[$key] = $locators->buildFromConfig($locatorConfig);
        }
    }
    
    
    public function getTemplateFile($name)
    {
        $split = explode(':', $name, 2);
        if(count($split) > 1) {
            list($prefix, $name) = $split;
            
        }else{
            $prefix = $this->defaultPrefix;
            $name = $name;
        }
        
        return $this->locators[$prefix]->getTemplateFile($name);
    }
}
