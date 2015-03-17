<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Runtime
 */
class RuntimeTest extends \PHPixie\Test\Testcase
{
    protected $file;
    protected $arrayData;
    
    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'phpixie_template_test');
        $this->arrayData = $this->quickMock('\PHPixie\Slice\Type\ArrayData');
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->runtime();
    }
    
    /**
     * @covers ::run
     * @covers ::getLayout
     * @covers ::getBlocks
     * @covers ::<protected>
     */
    public function testRuntime()
    {
        $this->runtimeTest(
            '<?php echo $name; ?>',
            array(
                'content' => 'Trixie',
            ),
            array(),
            array(),
            null,
            array(
                'name' => 'Trixie'
            )
        );
        
        $this->runtimeTest(
            '<?php echo $this->get("name"); ?> 
            <?php echo $this->get("flowers", 5); ?>
            <?php $this->set("magic", "Rain"); ?>
            <?php $this->remove("magic"); ?>
            ',
            array(
                'content' => 'Trixie 
                6',
            ),
            array(),
            array(
                array('get', array('name'), 'Trixie'),
                array('get', array('flowers', 5), 6),
                array('set', array('magic', 'Rain')),
                array('remove', array('magic'))
            )
        );
        
        $this->runtimeTest(
            '<?php echo $this->data()->get("name"); ?>',
            array(
                'content' => 'Trixie',
            ),
            array(),
            array(
                array('get', array('name'), 'Trixie')
            )
        );
        
        $this->runtimeTest(
            'Fairy
            <?php echo $this->childContent(); ?> 
            Trixie',
            array(
                'content' => 'Fairy
                Pixie 
                Trixie'
            ),
            array(),
            array(),
            'Pixie'
        );
        
        $this->runtimeTest(
            '<?php echo $this->block("tree"); ?>
            <?php echo $this->block("pixies"); ?>',
            array(
                'content' => 'Pixie',
                'blocks'  => array(
                    'tree' => 'Pixie',
                )
            ),
            array(
                'tree' => 'Pixie'
            )
        );
        
        
        $this->runtimeTest(
            '<?php $this->layout("pixie"); ?>',
            array(
                'layout'  => 'pixie',
            )
        );
        
        $this->runtimeTest(
            '<?php $this->startBlock("fairy"); ?>
                Trixie
            <?php $this->endBlock(); ?>
            <?php if($this->startBlock("tree", true)): ?>
                Tree
            <?php $this->endBlock(); endif; ?>
            ',
            array(
                'blocks'  => array(
                    'fairy' => 'Trixie
                    Pixie',
                    'tree'  => 'Oak'
                )
            ),
            array(
                'fairy' => 'Pixie',
                'tree'  => 'Oak'
            )
        );
        
        $this->runtimeTest(
            '<?php $this->startBlock("tree"); ?>
                Tree
            <?php $this->endBlock(); ?>
            <?php $this->startBlock("meadow"); ?>
                Flower
            <?php $this->endBlock(); ?>
            Blum
            ',
            array(
                'content' => 'Blum',
                'blocks'  => array(
                    'tree' => 'Tree',
                    'meadow' => 'Flower'
                )
            )
        );
        
        $this->runtimeTest(
            '<?php $this->startBlock("tree"); ?>
                Tree
            <?php $this->endBlock(); ?>
            <?php $this->endBlock(); ?>
            ',
            'exception'
        );
        
        
        $this->runtimeTest(
            '<?php $this->startBlock("tree"); ?>
            <?php $this->startBlock("fairy"); ?>
            <?php $this->endBlock(); ?>
            ',
            'exception'
        );
    }
    
    /**
     * @covers ::run
     * @covers ::getLayout
     * @covers ::getBlocks
     * @covers ::<protected>
     */
    public function testExceptionCleanup()
    {
        $this->exceptionCleanupTest('
            <?php $this->startBlock("pixie"); ?>
            <?php $this->startBlock("fairy"); ?>
            <?php throw new \Exception(); ?>
        ');
        
        $this->exceptionCleanupTest('
            <?php $this->startBlock("pixie"); ?>
            <?php $this->startBlock("fairy"); ?>
            <?php $this->endBlock(); ?>
        ');
        
        $this->exceptionCleanupTest('
            <?php $this->endBlock("fairy"); ?>
            <?php throw new \Exception(); ?>
        ');
    }
    
    protected function exceptionCleanupTest($template)
    {
        ob_start();
        echo(5);
        
        $runtime = $this->prepareRuntime($template);
        
        $this->assertException(function() use($runtime) {
            $runtime->run();
        }, '\Exception');
        
        $output = ob_get_clean();
        $this->assertSame('5', $output);
    }
    
    protected function runtimeTest($template, $expects, $blocks = array(), $arrayMethods = array(), $childContent = null, $data = array())
    {
        $runtime = $this->prepareRuntime($template, $blocks, $arrayMethods, $childContent, $data);
        if($expects == 'exception') {
            $this->assertException(function() use($runtime) {
                $runtime->run();
            }, '\PHPixie\Template\Exception');
            
        }else {
            $this->assertRuntime($expects, $runtime);
        }
    }
    
    protected function prepareRuntime($template, $blocks = array(), $arrayMethods = array(), $childContent = null, $data = array())
    {
        file_put_contents($this->file, $template);
        
        $this->method($this->arrayData, 'get', $data, array(), 0);
        foreach($arrayMethods as $key => $set) {
            $return = isset($set[2]) ? $set[2] : null;
            $this->method($this->arrayData, $set[0], $return, $set[1], $key+1);
        }
        
        return $this->runtime($childContent, $blocks);
    }
    
    protected function assertRuntime($expects, $runtime)
    {
        $expects = array_merge(array(
            'content' => '',
            'layout'  => null,
            'blocks'  => array()
        ), $expects);

        $expects['content'] = $this->trim($expects['content']);
        foreach($expects['blocks'] as $key => $block) {
            $expects['blocks'][$key] = $this->trim($block);
        }

        $content = $runtime->run();

        $resultBlocks = array();
        foreach($runtime->getBlocks() as $key => $block) {
            $resultBlocks[$key] = $this->trim($block);
        }

        $this->assertSame($expects, array(
            'content' => $this->trim($content),
            'layout'  => $runtime->getLayout(),
            'blocks'  => $resultBlocks
        ));
    }
    
    protected function trim($string)
    {
        $string = preg_replace('# +#', ' ', $string);
        return trim($string);
    }
    
    public function tearDown()
    {
        if(file_exists($this->file)) {
            unlink($this->file);
        }
    }
    
    protected function runtime($childContent = null, $blocks = array())
    {
        return new \PHPixie\Template\Runtime(
            $this->file,
            $this->arrayData,
            $childContent,
            $blocks
        );
    }
}