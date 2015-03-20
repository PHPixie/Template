<?php

namespace PHPixie\Tests\Template;

/**
 * @coversDefaultClass \PHPixie\Template\Container
 */
class ContainerTest extends \PHPixie\Test\Testcase
{
    protected $renderer;
    protected $templateName = 'pixie';
    protected $arrayData;
    
    protected $container;
    
    public function setUp()
    {
        $this->renderer  = $this->quickMock('\PHPixie\Template\Renderer');
        $this->arrayData = $this->quickMock('\PHPixie\Template\Container');
        
        $this->container = new \PHPixie\Template\Container(
            $this->renderer,
            $this->templateName,
            $this->arrayData
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
     * @covers ::templateName
     * @covers ::<protected>
     */
    public function testTemplateName()
    {
        $this->assertSame($this->templateName, $this->container->templateName());
    }
    
    /**
     * @covers ::data
     * @covers ::<protected>
     */
    public function testData()
    {
        $this->assertSame($this->arrayData, $this->container->data());
    }
    
    /**
     * @covers ::get
     * @covers ::set
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
            
            $this->method($this->arrayData, $set[0], $dataReturn, $dataParams, 0);
            
            $callback = array($this->container, $set[0]);
            $this->assertSame($return, call_user_func_array($callback, $set[1]));
        }
    }
    
    /**
     * @covers ::render
     * @covers ::<protected>
     */
    public function testRender()
    {
        $this->method($this->renderer, 'render', 'pixie', array($this->templateName, $this->arrayData), 0);
        $this->assertSame('pixie', $this->container->render());
    }
}