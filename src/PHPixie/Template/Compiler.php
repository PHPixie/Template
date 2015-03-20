<?php

namespace PHPixie\Template;

class Compiler
{
    protected $formats;
    protected $configData;
    
    protected $cacheDirectory;
    
    public function __construct($formats, $configData)
    {
        $this->formats    = $formats;
        $this->configData = $configData;
    }
    
    public function compile($path)
    {
        $format = $this->formats->getByFilename($path);
        
        if($format === null) {
            return $path;
        }
        
        $hash = crc32($path);
        $cachePath = $this->cacheDirectory().'/'.$hash.'.php';
        
        if(!file_exists($cachePath) || filemtime($cachePath) < filemtime($path)) {
            
            $compiled = $format->compile($path);
            file_put_contents($cachePath, $compiled);
        }
        
        return $cachePath;
    }
    
    protected function cacheDirectory()
    {
        if($this->cacheDirectory === null) {
            $this->cacheDirectory = $this->configData->getRequired('directory');
        }
        
        return $this->cacheDirectory;
    }
}