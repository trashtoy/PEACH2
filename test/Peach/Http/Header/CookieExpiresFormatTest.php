<?php
namespace Peach\Http\Header;

use Peach\DT\Timestamp;
use PHPUnit_Framework_TestCase;

class CookieExpiresFormatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CookieExpiresFormat
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = CookieExpiresFormat::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * getInstance() のテストです. 以下を確認します.
     * 
     * - CookieExpiresFormat のインスタンスを返すことを確認します.
     * - 常に同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\Http\Header\CookieExpiresFormat::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = CookieExpiresFormat::getInstance();
        $this->assertSame("Peach\\Http\\Header\\CookieExpiresFormat", get_class($obj1));
        $obj2 = CookieExpiresFormat::getInstance();
        $this->assertSame($obj1, $obj2);
    }
    
    /**
     * format() のテストです. 以下を確認します.
     * 
     * - "Wdy, DD-Mon-YY HH:MM:SS GMT" 形式の文字列を返すこと
     * - 引数のタイムゾーンに応じて表示時刻が GMT に補正されること
     * 
     * @covers Peach\Http\Header\CookieExpiresFormat::format
     * @covers Peach\Http\Header\CookieExpiresFormat::handleFormat
     * @covers Peach\Http\Header\CookieExpiresFormat::formatWeekday
     * @covers Peach\Http\Header\CookieExpiresFormat::formatMonth
     */
    public function testFormat()
    {
        $obj     = $this->object;
        $t       = new Timestamp(2012, 5, 21, 7, 34, 45);
        $expect1 = "Sun, 20-May-2012 22:34:45 GMT";
        $expect2 = "Mon, 21-May-2012 09:34:45 GMT";
        $this->assertSame($expect1, $obj->format($t, -540)); // UTC+9
        $this->assertSame($expect2, $obj->format($t, 120));  // UTC-2
    }
}
