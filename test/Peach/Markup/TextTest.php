<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestContext.php");

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Text
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Text("THIS IS TEST");
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * コンストラクタに指定した文字列を返すことを確認します.
     * 
     * @covers Peach\Markup\Text::__construct
     * @covers Peach\Markup\Text::getText
     */
    public function testGetText()
    {
        $obj = new Text("THIS IS TEST");
        $this->assertSame("THIS IS TEST", $obj->getText());
    }
    
    /**
     * Context の handleText() を呼び出すことを確認します.
     * 
     * @covers Peach\Markup\Text::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleText", $context->getResult());
    }
    
    /**
     * このオブジェクト自身を返すことを確認します.
     * 
     * @covers Peach\Markup\Text::getAppendee
     */
    public function testGetAppendee()
    {
        $obj = $this->object;
        $this->assertSame($obj, $obj->getAppendee());
    }
    
    /**
     * コンストラクタに指定した文字列を返すことを確認します.
     * 
     * @covers Peach\Markup\Text::__toString
     */
    public function test__toString()
    {
        $this->assertSame("THIS IS TEST", $this->object->__toString());
    }
}
