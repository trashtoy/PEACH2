<?php
namespace Peach\DT;

/**
 * Test class for ShiftFormat.
 * 
 * このテストでは, システム時刻の時差を UTC+9, フォーマットの時差を UTC-5 と仮定します.
 * (日本の Web サーバーで, ニューヨーク在住のユーザー向けの Web サイトを運営するようなケースです)
 * システム時刻とフォーマットの時差が 14 時間となるため,
 * 
 * - parse の際は, 変換結果を 14 時間遅らせたオブジェクト
 * - format の際は, 変換結果を 14 時間早めた文字列
 * 
 * となることを確認します.
 */
class ShiftFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShiftFormat
     */
    protected $object;
    
    /**
     * @var strings
     */
    private $defaultTZ;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->defaultTZ = date_default_timezone_get();
        date_default_timezone_set("Asia/Tokyo");
        $f = new SimpleFormat("Y.m.d H:i:s");
        $this->object = new ShiftFormat($f, 300, -540); // Out: Newyork, In: Japan
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        date_default_timezone_set($this->defaultTZ);
    }
    
    /**
     * 第二引数を省略した場合に, システム時刻の時差が設定されることを確認します.
     * @covers Peach\DT\ShiftFormat::__construct
     */
    public function test__construct()
    {
        $f        = W3cDatetimeFormat::getInstance();
        $test     = new ShiftFormat($f, 300);
        $expected = new ShiftFormat($f, 300, -540);
        $this->assertEquals($expected, $test);
    }
    
    /**
     * parseDate のテストです. オリジナルと同じ結果になることを確認します.
     * @covers Peach\DT\ShiftFormat::parseDate
     */
    public function testParseDate()
    {
        $this->assertEquals(new Date(2012, 5, 20), $this->object->parseDate("2012.05.20 17:30:45"));
    }
    
    /**
     * parseDatetime のテストです.
     * 表示時刻の 14 時間後の時間オブジェクトが生成されることを確認します.
     * @covers Peach\DT\ShiftFormat::parseDatetime
     * @covers Peach\DT\ShiftFormat::adjustFromParse
     */
    public function testParseDatetime()
    {
        $this->assertEquals(new Datetime(2012, 5, 21, 7, 30), $this->object->parseDatetime("2012.05.20 17:30:45"));
    }
    
    /**
     * parseTimestamp のテストです.
     * 表示時刻の 14 時間後の時間オブジェクトが生成されることを確認します.
     * @covers Peach\DT\ShiftFormat::parseTimestamp
     * @covers Peach\DT\ShiftFormat::adjustFromParse
     */
    public function testParseTimestamp()
    {
        $this->assertEquals(new Timestamp(2012, 5, 21, 7, 30, 45), $this->object->parseTimestamp("2012.05.20 17:30:45"));
    }
    
    /**
     * formatDate のテストです.
     * オリジナルと同じ結果を返すことを確認します.
     * @covers Peach\DT\ShiftFormat::formatDate
     */
    public function testFormatDate()
    {
        $this->assertSame("2012.05.21 00:00:00", $this->object->formatDate(new Date(2012, 5, 21)));
    }
    
    /**
     * formatDatetime のテストです.
     * 表示時刻の 14 時間前のフォーマットが出力されることを確認します.
     * @covers Peach\DT\ShiftFormat::formatDatetime
     * @covers Peach\DT\ShiftFormat::adjustFromFormat
     */
    public function testFormatDatetime()
    {
        $this->assertSame("2012.05.20 17:30:00", $this->object->formatDatetime(new Datetime(2012, 5, 21, 7, 30)));
    }
    
    /**
     * formatTimestamp のテストです.
     * 表示時刻の 14 時間前のフォーマットが出力されることを確認します.
     * @covers Peach\DT\ShiftFormat::formatTimestamp
     * @covers Peach\DT\ShiftFormat::adjustFromFormat
     */
    public function testFormatTimestamp()
    {
        $this->assertSame("2012.05.20 17:30:45", $this->object->formatTimestamp(new Timestamp(2012, 5, 21, 7, 30, 45)));
    }
}
