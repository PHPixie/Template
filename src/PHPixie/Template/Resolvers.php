<?php

namespace PHPixie\Template;

class Resolvers
{
    public function directory($directory, $defaultExtension = 'php')
    {
        return new Resolvers\Resolver\Directory($directory, $defaultExtension);
    }
}