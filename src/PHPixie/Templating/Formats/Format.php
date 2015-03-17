<?php
namespace \PHPixie\Templating\Formats;

interface Format
{
    public function getCompiledFilePath($file);
}