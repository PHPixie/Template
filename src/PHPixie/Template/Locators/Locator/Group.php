<?php

namespace PHPixie\Template\Locators\Locator;

class Group
{
    protected $locators = array();
    
    public function __construct($locators, $configData)
    {
        $locatorsConfig = $configData->slice('locators');
        foreach($locatorsConfig->keys(null, true) as $key) {
            $locatorConfig = $locatorsConfig->slice($key);
            $this->locators[] = $locators->buildFromConfig($locatorConfig);
        }
    }
    
    public function getTemplateFile($name)
    {
        $path = null;
        foreach($this->locators as $locator) {
            $path = $locator->getTemplateFile($name);
            if($path !== null) {
                break;
            }
        }
        
        return $path;
    }
}