<?php

namespace PHPixie\Template\Resolvers;

interface Resolver{
    public function getTemplateFile($name);
}