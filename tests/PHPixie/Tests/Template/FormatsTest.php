<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Formats
 */
class FormatsTest extends \PHPixie\Test\Testcase
{
    protected $externalFormats = array();
    protected $formats;
    
    public function setUp()
    {
        for($i=0;$i<2;$i++) {
            $this->externalFormats[]= $this->abstractMock('\PHPixie\Template\Formats\Format');
        }
        
        $this->method($this->externalFormats[0], 'handledExtensions', array('php', 'html'), array(), 0);
        $this->method($this->externalFormats[1], 'handledExtensions', array('php', 'haml'), array(), 0);
        
        $this->formats = new \PHPixie\Template\Formats($this->externalFormats);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    public function testGetByFilename()
    {
        $map = array(
            'html' => 0,
            'haml' => 1,
            'php'  => 1,
            'md'   => null,
        );
        
        foreach($map as $extension => $format) {
            if($format !== null) {
                $format = $this->externalFormats[$format];
            }
            
            $this->assertSame($format, $this->formats->getByFilename('file.'.$extension));
        }
    }
}