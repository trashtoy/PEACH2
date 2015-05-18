<?php
namespace Peach\DF\JsonCodec;

class MemberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Member
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Member();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * インスタンス化直後はキーと値が null となっていることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\Member::__construct
     * @covers Peach\DF\JsonCodec\Member::getKey
     * @covers Peach\DF\JsonCodec\Member::getValue
     */
    public function test__construct()
    {
        $expr = $this->object;
        $this->assertNull($expr->getKey());
        $this->assertNull($expr->getValue());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Member::handle
     * @covers Peach\DF\JsonCodec\Member::getKey
     * @covers Peach\DF\JsonCodec\Member::getValue
     */
    public function testHandle()
    {
        $context = new Context('"hoge" : 135');
        $expr    = $this->object;
        $expr->handle($context);
        $this->assertSame("hoge", $expr->getKey());
        $this->assertSame(135, $expr->getValue());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Member::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNoSeparator()
    {
        $context = new Context('"hoge"  ');
        $expr    = $this->object;
        $expr->handle($context);
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Member::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNoValue()
    {
        $context = new Context('"hoge":   ');
        $expr    = $this->object;
        $expr->handle($context);
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Member::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testHandleFailByNoString()
    {
        $context = new Context('hoge : 135 ');
        $expr    = $this->object;
        $expr->handle($context);
    }
}
