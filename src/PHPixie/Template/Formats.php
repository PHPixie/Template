<?php

namespace PHPixie\Template;

class Formats
{
    protected $extensionMap = array();
    
    public function __construct($externalFormats = array())
    {
        foreach($externalFormats as $format) {
            foreach($format->handledExtensions() as $extension) {
               $this->extensionMap[$extension] = $format; 
            }
        }
    }
    
    public function getByFilename($file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if(!array_key_exists($extension, $this->extensionMap)) {
            return null;
        }
        
        return $this->extensionMap[$extension];
    }
}