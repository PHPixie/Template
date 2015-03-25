<?php

namespace PHPixie\Template\Formats;

interface Format
{
    public function handledExtensions();
    public function compile($contents);
}