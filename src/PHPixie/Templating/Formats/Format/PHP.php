<?php
namespace \PHPixie\Templating\Formats\Format;

class PHP implements \PHPixie\Templating\Formats\Format
{
    public function getCompiledFilePath($file)
    {
        return $file;
    }
}