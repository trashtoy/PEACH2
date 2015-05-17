<?php
namespace Peach\DF\JsonCodec;

class StructuralCharTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
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
     * @covers Peach\DF\JsonCodec\StructuralChar::__construct
     * @covers Peach\DF\JsonCodec\StructuralChar::getResult
     */
    public function test__construct()
    {
        $expr = new StructuralChar(array(",", "}"));
        $this->assertNull($expr->getResult());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\StructuralChar::handle
     * @covers Peach\DF\JsonCodec\StructuralChar::handleChar
     * @covers Peach\DF\JsonCodec\StructuralChar::getResult
     */
    public function testHandleAndGetResult()
    {
        $context = new Context("    }    ");
        $expr    = new StructuralChar(array(",", "}"));
        $expr->handle($context);
        $this->assertSame("}", $expr->getResult());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\StructuralChar::handle
     * @expectedException Peach\DF\JsonCodec\DecodeException
     * @expectedExceptionMessage Unexpected end of JSON at line 3, column 1
     */
    public function testHandleFailByEndOfJson()
    {
        $context = new Context("    \r\n    \n");
        $expr    = new StructuralChar(array(",", "}"));
        $expr->handle($context);
    }
    
    /**
     * @covers Peach\DF\JsonCodec\StructuralChar::handle
     * @covers Peach\DF\JsonCodec\StructuralChar::handleChar
     * @expectedException Peach\DF\JsonCodec\DecodeException
     * @expectedExceptionMessage 'x' is not allowed (expected: ',', '}') at line 3, column 4
     */
    public function testHandleFailByInvalidChar()
    {
        $context = new Context("    \n        \r   x   \r\n    ");
        $expr    = new StructuralChar(array(",", "}"));
        $expr->handle($context);
    }
}
