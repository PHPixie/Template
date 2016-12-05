<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Compiler
 */
class CompilerTest extends \PHPixie\Test\Testcase
{
    protected $filesystemRoot;
    protected $configData;
    protected $formats;
    protected $compiler;
    
    protected $directory;
    protected $cacheDirectory;
    
    public function setUp()
    {
        $this->filesystemRoot = $this->quickMock('\PHPixie\Filesystem\Root');
        $this->configData     = $this->quickMock('\PHPixie\Slice\Data');
        $this->formats        = $this->quickMock('\PHPixie\Template\Formats');
        
        $this->compiler = new \PHPixie\Template\Compiler(
            $this->filesystemRoot,
            $this->formats,
            $this->configData
        );
        
        $this->directory = sys_get_temp_dir().'/phpixie_template_test';
        $this->cacheDirectory = $this->directory.'/cache';
        
        $this->removeDirectory();
        mkdir($this->directory);
        mkdir($this->cacheDirectory);
    }
    
    public function tearDown()
    {
        $this->removeDirectory();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::compile
     * @covers ::<protected>
     */
    public function testCompile()
    {
        $this->compileTest(false);
        
        $this->method($this->configData, 'getRequired', $this->cacheDirectory, array('cacheDirectory'), 0);
        $this->method($this->filesystemRoot, 'path', $this->cacheDirectory, array($this->cacheDirectory), 0);
        $this->compileTest(true);
        $this->compileTest(true, true);
        $this->compileTest(true, true, true);
    }
    
    protected function compileTest($hasFormat = true, $cacheExists = false, $isFileModified = false)
    {
        $path = $this->directory.'/test.php';
        
        if($hasFormat) {
            $format = $this->abstractMock('\PHPixie\Template\Formats\Format');
        }else{
            $format = null;
        }
        
        $this->method($this->formats, 'getByFilename', $format, array($path), 0);
        
        if($hasFormat) {
            $cachedPath = $this->cacheDirectory.'/'.crc32($path).'.php';
            
            if($cacheExists) {
                file_put_contents($cachedPath, 'old');
                
            }else{
                if(file_exists($cachedPath)) {
                    unlink($cachedPath);
                }
            }
            
            if(!$isFileModified) {
                sleep(1);
            }
            
            file_put_contents($path, '');
            
            $recompile = !$cacheExists || !$isFileModified;
            if($recompile) {
                $contents = 'updated';
                $this->method($format, 'compile', $contents, array($path), 0);
                
            }else{
                $contents = 'old';
            }
            
        }else{
            $cachedPath = $path;
        }
        
        $this->assertSame($cachedPath, $this->compiler->compile($path));
        
        if($hasFormat) {
            $this->assertSame($contents, file_get_contents($cachedPath));
        }
    }
    
    protected function removeDirectory()
    {
        $dirs = array(
            $this->cacheDirectory,
            $this->directory
        );
        
        foreach($dirs as $dir) {
            if(is_dir($dir)) {
                foreach(scandir($dir) as $file) {
                    $file = $dir.'/'.$file;
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
                
                rmdir($dir);
            }
        }
    }
}