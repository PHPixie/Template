<?php

namespace PHPixie\Tests\Template\Extensions\Extension;

/**
 * @coversDefaultClass \PHPixie\Template\Extensions\Extension\HTML
 */
class HTMLTest extends \PHPixie\Test\Testcase
{
    protected $html;
    
    public function setUp()
    {
        $this->html = new \PHPixie\Template\Extensions\Extension\HTML();
    }

    /**
     * @covers ::name
     * @covers ::<protected>
     */
    public function testName()
    {
        $this->assertSame('html', $this->html->name());
    }
    
    /**
     * @covers ::methods
     * @covers ::<protected>
     */
    public function testMethods()
    {
        $this->assertSame(array(
            'htmlEscape' => 'escape',
            'htmlOutput' => 'output',
            'if'         => 'shortIf'
        ), $this->html->methods());
    }
    
    /**
     * @covers ::aliases
     * @covers ::<protected>
     */
    public function testAliases()
    {
        $this->assertSame(array(
            '_' => 'escape'
        ), $this->html->aliases());
    }
    
    /**
     * @covers ::escape
     * @covers ::<protected>
     */
    public function testEscape()
    {
        $this->assertSame("&lt;a href=&#039;test&#039;&gt;", $this->html->escape("<a href='test'>"));
    }
    
    /**
     * @covers ::output
     * @covers ::<protected>
     */
    public function testOutput()
    {
        ob_start();
        $this->html->output("<a href='test'>");
        $this->assertSame("&lt;a href=&#039;test&#039;&gt;", ob_get_clean());
    }
}