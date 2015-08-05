<?php
namespace Peach\Markup;
require_once(__DIR__ . "/ElementTest.php");
require_once(__DIR__ . "/ContainerTestImpl.php");
require_once(__DIR__ . "/TestContext.php");

class ContainerElementTest extends ElementTest
{
    /**
     * @var ContainerElement
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ContainerElement("testTag");
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 要素名が空文字列だった場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException \InvalidArgumentException
     * @covers Peach\Markup\ContainerElement::__construct
     * @covers Peach\Markup\EmptyElement::cleanNameString
     */
    public function test__constructFail()
    {
        new ContainerElement("");
    }
    
    /**
     * Container で定義されている append() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\ContainerElement::append
     */
    public function testAppend()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testAppend();
    }
    
    /**
     * Container で定義されている getChildNodes() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\ContainerElement::getChildNodes
     * @see    Peach\Markup\ContainerTestImpl::testChildNodes
     */
    public function testGetChildNodes()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testGetChildNodes();
    }
    
    /**
     * Context の handleContainerElement() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\ContainerElement::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleContainerElement", $context->getResult());
    }
    
    /**
     * 追加されたノードの個数を返すことを確認します.
     * 
     * @covers Peach\Markup\ContainerElement::size
     */
    public function testSize()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testSize();
    }
    
    /**
     * コンストラクタに指定した文字列を返すことを確認します.
     * @covers Peach\Markup\ContainerElement::getName
     */
    public function testGetName()
    {
        $obj   = $this->object;
        $this->assertSame("testTag", $obj->getName());
    }
}
