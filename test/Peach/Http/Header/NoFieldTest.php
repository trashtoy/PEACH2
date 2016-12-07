<?php
namespace Peach\Http\Header;

class NoFieldTest extends \PHPUnit_Framework_TestCase
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
     * 空文字列を返すことを確認します.
     * 
     * @covers Peach\Http\Header\NoField::format
     */
    public function testFormat()
    {
        $obj = NoField::getInstance();
        $this->assertSame("", $obj->format());
    }

    /**
     * 空文字列を返すことを確認します.
     * 
     * @covers Peach\Http\Header\NoField::getName
     */
    public function testGetName()
    {
        $obj = NoField::getInstance();
        $this->assertSame("", $obj->getName());
    }
    
    /**
     * null を返すことを確認します.
     * 
     * @covers Peach\Http\Header\NoField::getValue
     */
    public function testGetValue()
    {
        $obj = NoField::getInstance();
        $this->assertNull($obj->getValue());
    }
    
    /**
     * getInstance() のテストです. 以下を確認します.
     * 
     * - NoField オブジェクトを返す
     * - 複数回実行した際に同一のオブジェクトを返す
     * 
     * @covers Peach\Http\Header\NoField::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = NoField::getInstance();
        $obj2 = NoField::getInstance();
        $this->assertInstanceOf("Peach\\Http\\Header\\NoField", $obj1);
        $this->assertSame($obj1, $obj2);
    }
}
