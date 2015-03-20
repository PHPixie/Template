<?php

namespace PHPixie\Template\Extensions;

interface Extension
{
    public function name();
    public function methods();
    public function aliases();
}