<?php

namespace PHPixie\Tests\Template\Resolvers\Resolver;

/**
 * @coversDefaultClass \PHPixie\Template\Resolvers\Resolver\Directory
 */
class DirectoryTest extends \PHPixie\Test\Testcase
{
    protected $directory = '/phpixie/test';
    protected $defaultExtension = 'php';
    
    protected $resolver;
    
    public function setUp()
    {
        $this->resolver = new \PHPixie\Template\Resolvers\Resolver\Directory(
            $this->directory,
            $this->defaultExtension
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::getTemplateFile
     * @covers ::<protected>
     */
    public function testGetTemplateFile()
    {
        $file = $this->resolver->getTemplateFile('fairy');
        $this->assertSame($this->directory.'/fairy.php', $file);
        
        $file = $this->resolver->getTemplateFile('fairy.haml');
        $this->assertSame($this->directory.'/fairy.haml', $file);
    }
}