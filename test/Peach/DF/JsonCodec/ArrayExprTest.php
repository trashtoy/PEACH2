<?php
namespace Peach\DF\JsonCodec;

use Peach\Util\ArrayMap;

class ArrayExprTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayExpr
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ArrayExpr();
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
     * @covers Peach\DF\JsonCodec\ArrayExpr::__construct
     * @covers Peach\DF\JsonCodec\ArrayExpr::getResult
     */
    public function test__construct()
    {
        $expr = $this->object;
        $this->assertNull($expr->getResult());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\ArrayExpr::handle
     * @covers Peach\DF\JsonCodec\ArrayExpr::getResult
     */
    public function testHandleAndGetResult()
    {
        $context  = new Context('  [ 3.14 , true,  "test" ,null  ]   }  ', new ArrayMap());
        $expected = array(3.14, true, "test", null);
        $expr     = $this->object;
        $expr->handle($context);
        $this->assertSame($expected, $expr->getResult());
        $this->assertSame("}", $context->current());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\ArrayExpr::handle
     * @covers Peach\DF\JsonCodec\ArrayExpr::getResult
     */
    public function testHandleEmpty()
    {
        $context = new Context('  [     ]   ,  ', new ArrayMap());
        $expr    = $this->object;
        $expr->handle($context);
        $this->assertSame(array(), $expr->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * 配列の末尾にカンマが存在する場合にエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ArrayExpr::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByLastComma()
    {
        $context = new Context(" [ 1, 3, 5, ]", new ArrayMap());
        $expr    = $this->object;
        $expr->handle($context);
    }
    
    /**
     * 値が来るべき場所で JSON が終わってしまった場合にエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ArrayExpr::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNoValue()
    {
        $context = new Context(" [ 1, 3, 5, ", new ArrayMap());
        $expr    = $this->object;
        $expr->handle($context);
    }
    
    /**
     * "]" または "," が来るべき場所で JSON が終わってしまった場合にエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\ArrayExpr::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNoStructuralChar()
    {
        $context = new Context(" [ 1, 3, 5 ", new ArrayMap());
        $expr    = $this->object;
        $expr->handle($context);
    }
}
