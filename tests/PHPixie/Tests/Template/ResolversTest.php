<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Resolvers
 */
class ResolversTest extends \PHPixie\Test\Testcase
{
    protected $resolvers;
    
    public function setUp()
    {
        $this->resolvers = new \PHPixie\Template\Resolvers();
    }
    
    /**
     * #covers ::directory
     * #covers ::<protected>
     */
    public function testDirectory()
    {
        $resolver = $this->resolvers->directory('/fairy');
        $this->assertInstance($resolver, '\PHPixie\Template\Resolvers\Resolver\Directory', array(
            'directory' => '/fairy',
            'defaultExtension' => 'php'
        ));
        
        $resolver = $this->resolvers->directory('/fairy', 'haml');
        $this->assertInstance($resolver, '\PHPixie\Template\Resolvers\Resolver\Directory', array(
            'directory' => '/fairy',
            'defaultExtension' => 'haml'
        ));
    }
}