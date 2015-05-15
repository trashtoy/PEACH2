<?php
namespace Peach\DF\JsonCodec;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Value
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Value();
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
     * @covers Peach\DF\JsonCodec\Value::__construct
     */
    public function test__construct()
    {
        $value = new Value();
        $this->assertNull($value->getResult());
    }
    
    /**
     * リテラル "true" の解析テストです. 以下を確認します.
     * 
     * - "true" を decode した結果, 真偽値 TRUE に変換されること
     * - 引数の Context の index が 4 進むこと
     * 
     * @covers Peach\DF\JsonCodec\Value::handle
     * @covers Peach\DF\JsonCodec\Value::decodeLiteral
     * @covers Peach\DF\JsonCodec\Value::getResult
     */
    public function testHandleTrue()
    {
        $value   = $this->object;
        $context = new Context("true}");
        $value->handle($context);
        $this->assertSame(true, $value->getResult());
        $this->assertSame("}", $context->current());
    }
    
    /**
     * リテラル "false" の解析テストです. 以下を確認します.
     * 
     * - "false" を decode した結果, 真偽値 FALSE に変換されること
     * - 引数の Context の index が 5 進むこと
     * 
     * @covers Peach\DF\JsonCodec\Value::handle
     * @covers Peach\DF\JsonCodec\Value::decodeLiteral
     * @covers Peach\DF\JsonCodec\Value::getResult
     */
    public function testHandleFalse()
    {
        $value   = $this->object;
        $context = new Context("false,");
        $value->handle($context);
        $this->assertSame(false, $value->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * リテラル "null" の解析テストです. 以下を確認します.
     * 
     * - "null" を decode した結果, NULL に変換されること
     * - 引数の Context の index が 4 進むこと
     * 
     * @covers Peach\DF\JsonCodec\Value::handle
     * @covers Peach\DF\JsonCodec\Value::decodeLiteral
     * @covers Peach\DF\JsonCodec\Value::getResult
     */
    public function testHandleNull()
    {
        $value   = $this->object;
        $context = new Context("null{");
        $value->handle($context);
        $this->assertNull($value->getResult());
        $this->assertSame("{", $context->current());
    }
    
    /**
     * 文字列の解析テストです. 以下を確認します.
     * 
     * - 二重引用符で囲まれた文字列が結果として返されること
     * - 引数の Context の index が文字列長 + 2 だけ進むこと
     */
    public function testHandleString()
    {
        $value   = $this->object;
        $context = new Context('"Test",');
        $value->handle($context);
        $this->assertSame("Test", $value->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * 数値の解析テストです. 以下を確認します.
     * 
     * - その数値文字列が表現する値に等しい結果を返すこと
     * - 引数の Context の index が文字列長だけ進むこと
     */
    public function testHandleNumber()
    {
        $value   = $this->object;
        $context = new Context("3e+5,");
        $value->handle($context);
        $this->assertSame(300000.0, $value->getResult());
        $this->assertSame(",", $context->current());
    }
    
    /**
     * 不正な文字列を検知した場合に DecodeException をスローすることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\Value::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFail()
    {
        $value   = $this->object;
        $context = new Context("asdf");
        $value->handle($context);
    }
    
    /**
     * リテラルの解析中に不正な文字列を検知した場合に DecodeException をスローすることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\Value::handle
     * @covers Peach\DF\JsonCodec\Value::decodeLiteral
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testDecodeLiteralFail()
    {
        $value   = $this->object;
        $context = new Context("trap");
        $value->handle($context);
    }
}
