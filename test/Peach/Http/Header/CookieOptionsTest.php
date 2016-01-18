<?php
namespace Peach\Http\Header;

use PHPUnit_Framework_TestCase;
use Peach\DT\Timestamp;

class CookieOptionsTest extends PHPUnit_Framework_TestCase
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
        $timestamp = new Timestamp(2012, 5, 21, 7, 34, 45);
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
    
    /**
     * formatOptions() のテストです. 以下を確認します.
     * 
     * - デフォルトの状態では空の配列を返すこと
     * 
     * @covers Peach\Http\Header\CookieOptions::formatOptions
     */
    public function testFormatOptions()
    {
        $obj = $this->object;
        $this->assertSame(array(), $obj->formatOptions());
    }
    
    /**
     * expires 属性が書式化されることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::formatExpires
     */
    public function testFormatExpires()
    {
        $expected = array("expires=Sun, 20-May-2012 22:34:45 GMT");
        $obj      = $this->object;
        $obj->setExpires(new Timestamp(2012, 5, 21, 7, 34, 45));
        $obj->setTimeZoneOffset(-540);
        $this->assertSame($expected, $obj->formatOptions());
    }
    
    /**
     * setMaxAge() および getMaxAge() のテストです. 以下を確認します.
     * 
     * - setMaxAge() でセットした値が getMaxAge() から取得できること
     * - デフォルトでは null を返すこと
     * - 整数以外の値をセットした場合は整数に変換されること
     * - 0 未満の値をセットした場合は 0 に変換されること
     * - 一度セットした値を null で初期化できること
     * 
     * @covers Peach\Http\Header\CookieOptions::getMaxAge
     * @covers Peach\Http\Header\CookieOptions::setMaxAge
     */
    public function testAccessMaxAge()
    {
        $obj1 = new CookieOptions();
        $obj1->setMaxAge(3600);
        $this->assertSame(3600, $obj1->getMaxAge());
        
        $obj2 = new CookieOptions();
        $this->assertNull($obj2->getMaxAge());
        
        $obj3 = new CookieOptions();
        $obj3->setMaxAge("1234.56789");
        $this->assertSame(1234, $obj3->getMaxAge());
        
        $obj4 = new CookieOptions();
        $obj4->setMaxAge(-1800);
        $this->assertSame(0, $obj4->getMaxAge());
        
        $obj5 = new CookieOptions();
        $obj5->setMaxAge(1800);
        $this->assertNotNull($obj5->getMaxAge());
        $obj5->setMaxAge(null);
        $this->assertNull($obj5->getMaxAge());
    }
}
