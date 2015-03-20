<?php

namespace PHPixie\Tests\Template\Helpers\Helper;

/**
 * @coversDefaultClass \PHPixie\Template\Helpers\Helper\HTML
 */
class HTMLTest extends \PHPixie\Test\Testcase
{
    protected $htmlHelper;
    
    public function setUp()
    {
        $this->htmlHelper = new \PHPixie\Template\Helpers\Helper\HTML();
    }
    
    /**
     * @covers ::helperMethods
     * @covers ::<protected>
     */
    public function testHelperMethods()
    {
        $this->assertSame(array('escape', 'output'), $this->htmlHelper->helperMethods());
    }
    
    /**
     * @covers ::escape
     * @covers ::<protected>
     */
    public function testEscape()
    {
        $this->assertSame("&lt;a href=&#039;test&#039;&gt;", $this->htmlHelper->escape("<a href='test'>"));
    }
    
    /**
     * @covers ::output
     * @covers ::<protected>
     */
    public function testOutput()
    {
        ob_start();
        $this->htmlHelper->output("<a href='test'>");
        $this->assertSame("&lt;a href=&#039;test&#039;&gt;", ob_get_clean());
    }
}