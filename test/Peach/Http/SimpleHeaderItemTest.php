<?php
namespace Peach\Http;

use PHPUnit_Framework_TestCase;

class SimpleHeaderItemTest extends PHPUnit_Framework_TestCase
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
     * @covers Peach\Http\SimpleHeaderItem::__construct
     * @covers Peach\Http\SimpleHeaderItem::format
     */
    public function testFormat()
    {
        $obj = new SimpleHeaderItem("Server", "Apache");
        $this->assertSame("Apache", $obj->format());
    }
    
    /**
     * @covers Peach\Http\SimpleHeaderItem::__construct
     * @covers Peach\Http\SimpleHeaderItem::getName
     */
    public function testGetName()
    {
        $obj = new SimpleHeaderItem("Server", "Apache");
        $this->assertSame("Server", $obj->getName());
    }
}
