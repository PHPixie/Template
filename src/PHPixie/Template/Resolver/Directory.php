<?php

namespace PHPixie\Template\Resolver;

class Directory implements \PHPixie\Template\Resolver
{
    
    protected $directory;
    protected $defaultExtension;
    
    public function __construct($directory, $defaultExtension)
    {
        $this->directory        = $directory;
        $this->defaultExtension = $defaultExtension;
    }
    
    public function getTemplateFile($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if($extension === '')
        {
            $path.='.'.$this->defaultExtension;
        }
        
        return $path;
    }
}