<?php

namespace PHPixie\Template;

class Compiler
{
    protected $filesystem;
    protected $formats;
    protected $configData;
    
    protected $cacheDirectory;
    
    public function __construct($filesystem, $formats, $configData)
    {
        $this->filesystem = $filesystem;
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
            $path = $this->configData->getRequired('cacheDirectory');
            $this->cacheDirectory = $this->filesystem->rootPath($path);
        }
        
        return $this->cacheDirectory;
    }
}