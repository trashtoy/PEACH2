<?php
namespace Peach\Markup;

class MinimalBreakControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MinimalBreakControl
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = MinimalBreakControl::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * false を返すことを確認します.
     * @covers Peach\Markup\MinimalBreakControl::breaks
     */
    public function testBreaks()
    {
        $node = new ContainerElement("span");
        $node->appendNode("first text");
        $node->appendNode("second test");
        $this->assertFalse($this->object->breaks($node));
    }

    /**
     * getInstance() をテストします.
     * 以下を確認します.
     * 
     * - 返り値が MinimalBreakControl のインスタンスである
     * - どの返り値も, 同一のインスタンスを返す
     * 
     * @covers Peach\Markup\MinimalBreakControl::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = MinimalBreakControl::getInstance();
        $obj2 = MinimalBreakControl::getInstance();
        $this->assertSame("Peach\\Markup\\MinimalBreakControl", get_class($obj1));
        $this->assertSame($obj1, $obj2);
    }
}
