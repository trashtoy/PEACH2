<?php
namespace Peach\DT;

class FormatWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormatWrapper
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $original     = W3cDatetimeFormat::getInstance();
        $f            = new FormatWrapper($original);
        $this->object = $f;
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの引数と等しい時間オブジェクトを返すことを確認します.
     * @covers Peach\DT\FormatWrapper::getOriginal
     */
    public function testGetOriginal()
    {
        $original = new SimpleFormat("Y.n.d");
        $f        = new FormatWrapper($original);
        $this->assertSame($original, $f->getOriginal());
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::formatDate
     */
    public function testFormatDate()
    {
        $this->assertSame("2012-05-21", $this->object->formatDate(new Date(2012, 5, 21)));
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::formatDatetime
     */
    public function testFormatDatetime()
    {
        $this->assertSame("2012-05-21T07:30", $this->object->formatDatetime(new Datetime(2012, 5, 21, 7, 30)));
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::formatTimestamp
     */
    public function testFormatTimestamp()
    {
        $this->assertSame("2012-05-21T07:30:15", $this->object->formatTimestamp(new Timestamp(2012, 5, 21, 7, 30, 15)));
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::parseDate
     */
    public function testParseDate()
    {
        $this->assertEquals(new Date(2012, 5, 21), $this->object->parseDate("2012-05-21"));
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::parseDatetime
     */
    public function testParseDatetime()
    {
        $this->assertEquals(new Datetime(2012, 5, 21, 7, 30), $this->object->parseDatetime("2012-05-21T07:30"));
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\FormatWrapper::parseTimestamp
     */
    public function testParseTimestamp()
    {
        $this->assertEquals(new Timestamp(2012, 5, 21, 7, 30, 15), $this->object->parseTimestamp("2012-05-21T07:30:15"));
    }
}
