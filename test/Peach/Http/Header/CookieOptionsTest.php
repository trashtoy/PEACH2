<?php
namespace Peach\Http\Header;

class CookieOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CookieOptions
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CookieOptions();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * setExpires() でセットした Timestamp が getExpires() から取得できることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::getExpires
     * @covers Peach\Http\Header\CookieOptions::setExpires
     */
    public function testAccessExpires()
    {
        $obj       = $this->object;
        $timestamp = new \Peach\DT\Timestamp(2012, 5, 21, 7, 34, 45);
        $obj->setExpires($timestamp);
        $this->assertSame($timestamp, $obj->getExpires());
    }
    
    /**
     * setExpires() を一度も実行していない場合は null を返すことを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::getExpires
     */
    public function testGetExpiresByDefault()
    {
        $obj = new CookieOptions();
        $this->assertNull($obj->getExpires());
    }
    
    /**
     * setTimeZoneOffset() でセットした値が getTimeZoneOffset() から取得できることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::getTimeZoneOffset
     * @covers Peach\Http\Header\CookieOptions::setTimeZoneOffset
     */
    public function testAccessTimeZoneOffset()
    {
        $obj = $this->object;
        $obj->setTimeZoneOffset(-540);
        $this->assertSame(-540, $obj->getTimeZoneOffset());
    }
    
    /**
     * setTimeZoneOffset() を一度も実行していない場合は null を返すことを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::getTimeZoneOffset
     */
    public function testGetTimeZoneOffsetByDefault()
    {
        $obj = new CookieOptions();
        $this->assertNull($obj->getTimeZoneOffset());
    }
    
    /**
     * setTimeZoneOffset() で指定した値が -1425 以上 1425 以下に丸められることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setTimeZoneOffset
     */
    public function testSetTimeZoneOffsetByInvalidValue()
    {
        $obj = $this->object;
        $obj->setTimeZoneOffset(-2000);
        $this->assertSame(-1425, $obj->getTimeZoneOffset());
        $obj->setTimeZoneOffset(3000);
        $this->assertSame(1425, $obj->getTimeZoneOffset());
    }
}
