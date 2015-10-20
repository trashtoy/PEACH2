<?php
namespace Peach\Http\Header;

use PHPUnit_Framework_TestCase;

class RawTest extends PHPUnit_Framework_TestCase
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
     * 妥当なヘッダー名を引数に指定した場合に正常終了することを確認します.
     * 
     * @covers Peach\Http\Header\Raw::__construct
     */
    public function test__constructSuccess()
    {
        new Raw("Valid-Name", "hogehoge");
    }
    
    /**
     * 妥当でないヘッダー名を引数に指定した場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\Raw::__construct
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidName()
    {
        new Raw("Invalid/Name", "hogehoge");
    }
    
    /**
     * @covers Peach\Http\Header\Raw::__construct
     * @covers Peach\Http\Header\Raw::format
     */
    public function testFormat()
    {
        $obj = new Raw("Server", "Apache");
        $this->assertSame("Apache", $obj->format());
    }
    
    /**
     * @covers Peach\Http\Header\Raw::__construct
     * @covers Peach\Http\Header\Raw::getName
     */
    public function testGetName()
    {
        $obj = new Raw("Server", "Apache");
        $this->assertSame("Server", $obj->getName());
    }
}
