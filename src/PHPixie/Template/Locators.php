<?php

namespace PHPixie\Template;

class Locators
{
    protected $locators = array(
        'directory',
        'group',
        'prefix'
    );
    
    public function directory($configData)
    {
        return new Locators\Locator\Directory($configData);
    }
    
    public function group($configData)
    {
        return new Locators\Locator\Group($this, $configData);
    }
    
    public function prefix($configData)
    {
        return new Locators\Locator\Prefix($this, $configData);
    }
    
    public function buildFromConfig($configData) {
        $type = $configData->get('type', 'directory');
        if(!in_array($type, $this->locators, true)) {
            throw new \PHPixie\Template\Exception("Locator type '$type' does not exist");
        }
        
        return $this->$type($configData);
    }
}