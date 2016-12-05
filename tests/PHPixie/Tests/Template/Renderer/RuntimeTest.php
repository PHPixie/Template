<?php

namespace PHPixie\Tests\Template\Renderer;

/**
 * @coversDefaultClass \PHPixie\Template\Renderer\Runtime
 */
class RuntimeTest extends \PHPixie\Test\Testcase
{
    protected $context;
    
    protected $runtime;
    
    protected $renderer;
    protected $resolver;
    protected $data;
    protected $file;
    protected $fileAt = 0;
    
    public function setUp()
    {
        $this->context = $this->quickMock('\PHPixie\Template\Renderer\Context');
        
        $this->runtime = new \PHPixie\Template\Renderer\Runtime($this->context);
        
        $this->renderer = $this->quickMock('\PHPixie\Template\Renderer');
        $this->method($this->context, 'renderer', $this->renderer, array());
        
        $this->resolver = $this->quickMock('\PHPixie\Template\Resolver');
        $this->method($this->context, 'resolver', $this->resolver, array());
        
        $this->data = $this->quickMock('\PHPixie\Slice\Data\Editable');
        $this->method($this->context, 'data', $this->data, array());
    }
    
    public function tearDown()
    {
        if($this->file !== null && file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::run
     * @covers ::__call
     * @covers ::<protected>
     */
    public function testRun()
    {
        $this->renderTest('test', 'test');
        $this->renderTest('<?php echo $fairy; ?>', 'Pixie', 0, array('fairy' => 'Pixie'));
        
        $dataSets = array(
            array('get("pixie")', 'get', array('pixie', null), 'Trixie'),
            array('get("pixie", 5)', 'get', array('pixie', 5), 'Trixie'),
            array('set("pixie", 5)', 'set', array('pixie', 5)),
            array('remove("pixie")', 'remove', array('pixie'))
        );
        
        foreach($dataSets as $set) {
            $template = '<?php echo $this->'.$set[0].'; ?>';
            
            if(isset($set[3])) {
                $return  = $set[3];
                $content = $set[3];
            }else{
                $return  = null;
                $content = '';
            }
            
            $this->method($this->data, $set[1], $return, $set[2], 0);
            $this->renderTest($template, $content, 1);
        }
        
        $this->renderTest(
            '<?php echo $this->data() instanceof \PHPixie\Slice\Data\Editable; ?>',
            '1',
            1
        );
        
        $this->method($this->context, 'callExtensionMethod', 5, array('pixie', array(2, 3)), 3);
        $this->renderTest('<?php echo $this->pixie(2, 3); ?>', '5', 1);
        
        foreach(array('blockExists', 'extension') as $method) {
            $template = '<?php echo $this->'.$method.'("test"); ?>';
            $this->method($this->context, $method, 5, array('test'), 3);
            $this->renderTest($template, '5', 1);
        }
        
        $template = '<?php $this->block("test"); ?>';
        $this->method($this->context, 'block', 5, array('test'), 3);
        $this->renderTest($template, '5', 1);
        
        $template = '<?php $this->childContent(); ?>';
        $this->method($this->context, 'childContent', 5, array(), 3);
        $this->renderTest($template, '5', 1);
        
        $this->method($this->context, 'setLayout', 5, array('pixie'), 3);
        $this->renderTest('<?php $this->layout("pixie"); ?>', '', 1);
        
        $this->method($this->renderer, 'render', 5, array('pixie', array('t' => 1)), 0);
        $this->renderTest('<?php echo $this->render("pixie", array("t" => 1)); ?>', '5', 1);
        
        $this->method($this->resolver, 'resolve', 'pixie.php', array('pixie'), 1);
        $this->renderTest('<?php echo $this->resolve("pixie"); ?>', 'pixie.php', 1);
    }
    
    /**
     * @covers ::run
     * @covers ::<protected>
     */
    public function testRunBlocks()
    {
        $this->method($this->context, 'startBlock', true, array('pixie', true), 3);
        $this->method($this->context, 'endBlock', true, array('Fairy'), 4);
        $this->renderTest(
            '<?php $this->startBlock("pixie"); ?>'."\n".
            'Fairy'.
            '<?php $this->endBlock(); ?>',
        '', 2);
        
        $this->method($this->context, 'startBlock', true, array('pixie', false), 3);
        $this->method($this->context, 'endBlock', true, array('Fairy'), 4);
        $this->renderTest(
            '<?php $this->startBlock("pixie", false, true); ?>'."\n".
            'Fairy'.
            '<?php $this->endBlock(); ?>',
        '', 2);
        
        $this->method($this->context, 'blockExists', false, array('pixie'), 3);
        $this->method($this->context, 'startBlock', true, array('pixie', true), 4);
        $this->method($this->context, 'endBlock', true, array('Fairy'), 5);
        $this->renderTest(
            '<?php if($this->startBlock("pixie", true)): ?>'."\n".
            'Fairy'.
            '<?php $this->endBlock();'.
            'endif; ?>',
        '', 3);
        
        $this->method($this->context, 'blockExists', true, array('pixie'), 3);
        $this->renderTest(
            '<?php if($this->startBlock("pixie", true)): ?>'."\n".
            'Fairy'.
            '<?php $this->endBlock();'.
            'endif; ?>',
        '', 1);

    }
    
    /**
     * @covers ::run
     * @covers ::<protected>
     */
    public function testCleanUpException()
    {
        $this->method($this->context, 'startBlock', true, array('pixie', true), 3);
        $this->method($this->context, 'startBlock', true, array('pixie', true), 4);
        $this->prepareRun(
            '<?php $this->startBlock("pixie"); ?>'."\n".
            '<?php $this->startBlock("pixie"); ?>'."\n".
            '<?php throw new \Exception() ?>'."\n"
        );
        
        ob_start();
        echo(5);
        $runtime = $this->runtime;
        $this->assertException(function() use($runtime) {
            $runtime->run();
        }, '\Exception');
        echo(3);
        $this->assertSame('53', ob_get_clean());
    }
    
    protected function renderTest($template, $content, $processOffset = 0, $variables = array())
    {
        $contextAt = $this->prepareRun($template, $variables);
        
        $contextAt = $contextAt + $processOffset;
        $this->method($this->context, 'processRender', null, array($content), $contextAt++);
        $this->method($this->context, 'template', null, array(), $contextAt++);
        
        $this->assertSame($content, $this->runtime->run());
    }
    
    protected function prepareRun($template, $variables = array())
    {
        $contextAt = 0;
        
        $this->method($this->context, 'variables', $variables, array(), $contextAt++);
        $this->method($this->context, 'template', 'pixie', array(), $contextAt++);
        
        $contextAt++;
        
        $this->file = tempnam(sys_get_temp_dir(), 'phpixie_template'.$this->fileAt++);
        file_put_contents($this->file, $template);
        $this->method($this->resolver, 'resolve', $this->file, array('pixie'), 0);
        
        return $contextAt;
    }
}