<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestContext.php");
require_once(__DIR__ . "/ContainerTestImpl.php");

class NodeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeList
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new NodeList();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * Container で定義されている append() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\NodeList::append
     * @see    Peach\Markup\ContainerTestImpl::testAppend
     */
    public function testAppend()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testAppend();
    }
    
    /**
     * Context の handleNodeList() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\NodeList::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleNodeList", $context->getResult());
    }
    
    /**
     * 追加されたノードの個数を返すことを確認します.
     * 
     * @covers Peach\Markup\NodeList::size
     */
    public function testSize()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testSize();
    }
    
    /**
     * Container で定義されている getChildNodes() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\NodeList::getChildNodes
     * @see    Peach\Markup\ContainerTestImpl::testChildNodes
     */
    public function testGetChildNodes()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testGetChildNodes();
    }
}
