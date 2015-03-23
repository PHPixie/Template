<?php

namespace PHPixie\Template\Resolvers\Resolver;

class Group implements \PHPixie\Template\Resolvers\Resolver
{
    protected $resolvers;
    
    public function __construct($resolvers)
    {
        $this->resolvers = $resolvers;
    }
    
    public function getTemplateFile($name)
    {
        $path = null;
        
        foreach($this->resolvers as $resolver) {
            $path = $resolver->getTemplateFile($name);
            if($path !== null) {
                break;
            }
        }
        
        return $path;
    }
}
