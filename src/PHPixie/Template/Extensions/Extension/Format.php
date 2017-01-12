<?php

namespace PHPixie\Template\Extensions\Extension;

use \DateTime;

class Format implements \PHPixie\Template\Extensions\Extension
{
    public function name()
    {
        return 'format';
    }

    public function methods()
    {
        return array(
            'formatDate' => 'format'
        );
    }

    public function aliases()
    {
        return array();
    }

    public function format($date, $format)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    }
}