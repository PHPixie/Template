<?php

namespace PHPixie\Template\Locators\Locator;

class Directory implements \PHPixie\Template\Locators\Locator
{
    
    protected $directory;
    protected $defaultExtension;
    
    public function __construct($configData)
    {
        $this->directory        = $configData->getRequired('directory');
        $this->defaultExtension = $configData->get('defaultExtension', 'php');
    }
    
    public function getTemplateFile($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if($extension === '') {
            $path.='.'.$this->defaultExtension;
        }
        
        $path = $this->directory.'/'.$path;
        if(!file_exists($path)) {
            $path = null;
        }
        
        return $path;
    }
}