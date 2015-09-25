<?php
namespace Peach\Http\Header;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Status
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Status("404", "Not Found");
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの第 1 引数が空文字列として評価された場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::cleanCode
     * @expectedException InvalidArgumentException
     */
    public function test__constructFailByEmptyCode()
    {
        new Status("");
    }
    
    /**
     * 第 1 引数が空文字列として評価された場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::cleanCode
     * @expectedException InvalidArgumentException
     */
    public function test__constructFailByInvalidCode()
    {
        new Status("asdf");
    }
    
    /**
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::getCode
     */
    public function testGetCode()
    {
        $obj = $this->object;
        $this->assertSame("404", $obj->getCode());
    }
    
    /**
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::getReasonPhrase
     */
    public function testGetReasonPhrase()
    {
        $obj = $this->object;
        $this->assertSame("Not Found", $obj->getReasonPhrase());
    }
    
    /**
     * @covers Peach\Http\Header\Status::format
     */
    public function testFormat()
    {
        $obj = $this->object;
        $this->assertSame("404", $obj->format());
    }
    
    /**
     * 文字列 ":status" を返すことを確認します.
     * 
     * @covers Peach\Http\Header\Status::getName
     */
    public function testGetName()
    {
        $obj = $this->object;
        $this->assertSame(":status", $obj->getName());
    }
}
