<?php

namespace PHPixie\Tests\Template\Locators\Locator;

/**
 * @coversDefaultClass \PHPixie\Template\Locators\Locator\Directory
 */
class DirectoryTest extends \PHPixie\Test\Testcase
{
    protected $configData;
    
    protected $locator;
    
    protected $directory;
    protected $defaultExtension = 'php';
    
    public function setUp()
    {
        $this->directory = sys_get_temp_dir().'/phpixie_template/';
        
        $this->configData = $this->abstractMock('\PHPixie\Slice\Data');
        $this->method($this->configData, 'getRequired', $this->directory, array('directory'), 0);
        $this->method($this->configData, 'get', $this->defaultExtension, array('defaultExtension', 'php'), 1);
        
        $this->removeDirectory();
        
        mkdir($this->directory);
        
        $this->locator = new \PHPixie\Template\Locators\Locator\Directory(
            $this->configData
        );
    }
    
    public function tearDown()
    {
        $this->removeDirectory();
    }
    
    public function removeDirectory()
    {
        if(!is_dir($this->directory)) {
            return;
        }
        
        foreach(scandir($this->directory) as $file) {
            $file = $this->directory.'/'.$file;
            if(is_file($file) && file_exists($file)) {
                unlink($file);
            }
        }
        
        rmdir($this->directory);
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
        $file = $this->directory.'/fairy.php';
        file_put_contents($file, '');
        
        $this->assertSame($file, $this->locator->getTemplateFile('fairy'));
        
        $file = $this->directory.'/fairy.haml';
        file_put_contents($file, '');
        
        $this->assertSame($file, $this->locator->getTemplateFile('fairy.haml'));
        $this->assertSame(null, $this->locator->getTemplateFile('pixie.haml'));
    }
}