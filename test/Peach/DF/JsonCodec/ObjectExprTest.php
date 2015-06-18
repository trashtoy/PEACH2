<?php
namespace Peach\DF\JsonCodec;

use stdClass;
use Peach\Util\ArrayMap;

class ObjectExprTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectExpr
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ObjectExpr();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * インスタンス化直後は $result が null となっていることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ObjectExpr::__construct
     * @covers Peach\DF\JsonCodec\ObjectExpr::getResult
     */
    public function test__construct()
    {
        $expr = $this->object;
        $this->assertNull($expr->getResult());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\ObjectExpr::handle
     * @covers Peach\DF\JsonCodec\ObjectExpr::getResult
     */
    public function testHandleAndGetResult()
    {
        $context  = new Context('{ "a" : -3.14, "b": [true, false, true],"c" : "xxxx", "d": null  }   ,', new ArrayMap());
        $expr     = $this->object;
        $expected = new stdClass();
        $expected->a = -3.14;
        $expected->b = array(true, false, true);
        $expected->c = "xxxx";
        $expected->d = null;
        
        $expr->handle($context);
        $this->assertEquals($expected, $expr->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\ObjectExpr::handle
     * @covers Peach\DF\JsonCodec\ObjectExpr::getResult
     */
    public function testHandleEmpty()
    {
        $context  = new Context("    {\r\n    }   ,   ", new ArrayMap());
        $expr     = $this->object;
        $expr->handle($context);
        $this->assertEquals(new stdClass(), $expr->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * オブジェクトのキーが文字列でない場合にエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ObjectExpr::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNotStringKey()
    {
        $context  = new Context('{ test : "invalid" }', new ArrayMap());
        $expr     = $this->object;
        $expr->handle($context);
    }
    
    /**
     * "}" の直前に "," がある場合にエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ObjectExpr::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     * @expectedExceptionMessage Closing bracket after comma is not permitted at line 1, column 33
     */
    public function testHandleFailByCommaEnding()
    {
        $context  = new Context('{ "a" : 1 , "b" : 2 , "c" : 3 , }', new ArrayMap());
        $expr     = $this->object;
        $expr->handle($context);
    }
}
