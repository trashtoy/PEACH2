<?php
namespace Peach\Markup;
require_once(__DIR__ . "/ElementTest.php");
require_once(__DIR__ . "/TestContext.php");

class EmptyElementTest extends ElementTest
{
    /**
     * @var EmptyElement
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EmptyElement("testTag");
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
     * @covers Peach\Markup\EmptyElement::__construct
     * @covers Peach\Markup\EmptyElement::cleanNameString
     */
    public function test__constructFail()
    {
        new EmptyElement("");
    }
    
    /**
     * Context の handleEmptyElement() が呼び出されることを確認します.
     * @covers Peach\Markup\EmptyElement::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleEmptyElement", $context->getResult());
    }
    
    /**
     * コンストラクタに指定した文字列を返すことを確認します.
     * @covers Peach\Markup\EmptyElement::getName
     */
    public function testGetName()
    {
        $obj   = $this->object;
        $this->assertSame("testTag", $obj->getName());
    }
}
