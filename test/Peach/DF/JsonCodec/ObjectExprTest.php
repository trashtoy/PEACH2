<?php
namespace Peach\DF\JsonCodec;

use stdClass;
use Peach\DF\JsonCodec;
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
     * handle() および getResult() のテストです.
     * OBJECT_AS_ARRAY オプションの有無によって返される結果が stdClass と配列で変化することを確認します.
     * 
     * @param mixed   $expected
     * @param Context $context
     * @dataProvider  forTestHandleAndGetResult
     * @covers Peach\DF\JsonCodec\ObjectExpr::handle
     * @covers Peach\DF\JsonCodec\ObjectExpr::getResult
     * @covers Peach\DF\JsonCodec\ObjectExpr::getContainer
     * @covers Peach\DF\JsonCodec\ObjectExpr_ArrayContainer::__construct
     * @covers Peach\DF\JsonCodec\ObjectExpr_ArrayContainer::getResult
     * @covers Peach\DF\JsonCodec\ObjectExpr_ArrayContainer::setMember
     * @covers Peach\DF\JsonCodec\ObjectExpr_StdClassContainer::__construct
     * @covers Peach\DF\JsonCodec\ObjectExpr_StdClassContainer::getResult
     * @covers Peach\DF\JsonCodec\ObjectExpr_StdClassContainer::setMember
     */
    public function testHandleAndGetResult($expected, Context $context)
    {
        $expr = $this->object;
        $expr->handle($context);
        $this->assertEquals($expected, $expr->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * testHandleAndGetResult() のデータセットです.
     * OBJECT_AS_ARRAY オプションが有効の場合は配列, 無効の場合は stdClass
     * オブジェクトが期待される結果となります.
     * 
     * @return array
     */
    public function forTestHandleAndGetResult()
    {
        $context1  = new Context('{ "a" : -3.14, "b": [true, false, true],"c" : "xxxx", "d": null  }   ,', new ArrayMap());
        $expected1 = new stdClass();
        $expected1->a = -3.14;
        $expected1->b = array(true, false, true);
        $expected1->c = "xxxx";
        $expected1->d = null;
        
        $options   = new ArrayMap();
        $options->put(JsonCodec::OBJECT_AS_ARRAY, true);
        $context2  = new Context('{ "a" : -3.14, "b": [true, false, true],"c" : "xxxx", "d": null  }   ,', $options);
        $expected2 = array(
            "a" => -3.14,
            "b" => array(true, false, true),
            "c" => "xxxx",
            "d" => null,
        );
        
        return array(
            array($expected1, $context1),
            array($expected2, $context2),
        );
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
