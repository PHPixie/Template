<?php

namespace PHPixie\Template\Helpers\Helper;

class HTML implements \PHPixie\Template\Helpers\Helper
{
    public function helperMethods()
    {
        return array('escape', 'output');
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