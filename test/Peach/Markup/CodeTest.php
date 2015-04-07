<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestContext.php");

class CodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Code
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Code("THIS IS SAMPLE");
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
     * @covers Peach\Markup\Code::__construct
     * @covers Peach\Markup\Code::getText
     */
    public function testGetText()
    {
        $code = new Code("THIS IS SAMPLE");
        $this->assertSame("THIS IS SAMPLE", $code->getText());
    }
    
    /**
     * Context の handleCode() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\Code::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleCode", $context->getResult());
    }
    
    /**
     * コンストラクタに指定した文字列を返すことを確認します.
     * 
     * @covers Peach\Markup\Code::__toString
     */
    public function test__toString()
    {
        $this->assertSame("THIS IS SAMPLE", $this->object->__toString());
    }
}
