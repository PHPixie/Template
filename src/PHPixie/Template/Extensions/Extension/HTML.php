<?php

namespace PHPixie\Template\Extensions\Extension;

class HTML implements \PHPixie\Template\Extensions\Extension
{
    public function name()
    {
        return 'html';
    }
    
    public function methods()
    {
        return array(
            'htmlEscape' => 'escape',
            'htmlOutput' => 'output'
        );
    }
    
    public function aliases()
    {
        return array(
            '_' => 'escape'
        );
    }
    
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public function output($string)
    {
        echo $this->escape($string);
    }
}