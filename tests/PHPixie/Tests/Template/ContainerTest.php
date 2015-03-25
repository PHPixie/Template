<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Container
 */
class ContainerTest extends \PHPixie\Test\Testcase
{
    protected $renderer;
    protected $template = 'pixie';
    protected $data;
    
    protected $container;
    
    public function setUp()
    {
        $this->renderer = $this->quickMock('\PHPixie\Template\Renderer');
        $this->data     = $this->quickMock('\PHPixie\Slice\Data\Editable');
        
        $this->container = new \PHPixie\Template\Container(
            $this->renderer,
            $this->template,
            $this->data
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
     * @covers ::template
     * @covers ::<protected>
     */
    public function testTemplateName()
    {
        $this->assertSame($this->template, $this->container->template());
    }
    
    /**
     * @covers ::data
     * @covers ::<protected>
     */
    public function testData()
    {
        $this->assertSame($this->data, $this->container->data());
    }
    
    /**
     * @covers ::get
     * @covers ::set
     * @covers ::__get
     * @covers ::__set
     * @covers ::remove
     * @covers ::<protected>
     */
    public function testDataMethods()
    {
        $sets = array(
            array('get', array('test'), 5, array('test', null)),
            array('get', array('test', 3), 5),
            array('set', array('test', 3)),
            array('remove', array('test')),
        );
        
        foreach($sets as $set) {
            $dataParams = isset($set[3]) ? $set[3] : $set[1];
            
            if(isset($set[2])) {
                $dataReturn = $set[2];
                $return     = $set[2];
                
            }else{
                $dataReturn = null;
                $return     = $this->container;
            }
            
            $this->method($this->data, $set[0], $dataReturn, $dataParams, 0);
            
            $callback = array($this->container, $set[0]);
            $this->assertSame($return, call_user_func_array($callback, $set[1]));
        }
    }
    
    /**
     * @covers ::__get
     * @covers ::__set
     * @covers ::<protected>
     */
    public function testGetSet()
    {
        $this->method($this->data, 'set', null, array('pixie', 5), 0);
        $this->container->pixie = 5;
        
        $this->method($this->data, 'get', 5, array('pixie', null), 0);
        $this->assertSame(5, $this->container->pixie);
    }
    
    /**
     * @covers ::render
     * @covers ::<protected>
     */
    public function testRender()
    {
        $data = array('t' => 1);
        $this->method($this->data, 'get', $data, array(), 0);
        
        $this->method($this->renderer, 'render', 'pixie', array($this->template, $data), 0);
        $this->assertSame('pixie', $this->container->render());
    }
}