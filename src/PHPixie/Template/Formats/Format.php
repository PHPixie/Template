<?php

namespace PHPixie\Template\Formats;

/**
 * Format interface
 */
interface Format
{
    /**
     * Array of file extensions that this format supports
     * @return array
     */
    public function handledExtensions();

    /**
     * Compile a file into a PHP template
     * @param string $file File name
     * @return string
     */
    public function compile($file);
}