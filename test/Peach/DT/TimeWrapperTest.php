<?php
namespace Peach\DT;

class TimeWrapperTest extends \PHPUnit_Framework_TestCase
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
     * コンストラクタの引数と等しい時間オブジェクトを返すことを確認します.
     * @covers Peach\DT\TimeWrapper::getOriginal
     */
    public function testGetOriginal()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertEquals(new Timestamp(2012, 5, 21, 7, 30, 15), $d->getOriginal());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::getType
     */
    public function testGetType()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame(Time::TYPE_TIMESTAMP, $d->getType());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::before
     */
    public function testBefore()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertTrue($d->before(new Timestamp(2012, 5, 22, 0, 0, 0)));
        $this->assertFalse($d->before(new Timestamp(2012, 5, 20, 0, 0, 0)));
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::after
     */
    public function testAfter()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertFalse($d->after(new Timestamp(2012, 5, 22, 0, 0, 0)));
        $this->assertTrue($d->after(new Timestamp(2012, 5, 20, 0, 0, 0)));
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::compareTo
     */
    public function testCompareTo()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertLessThan(0, $d->compareTo(new Timestamp(2012, 5, 22, 0, 0, 0)));
        $this->assertGreaterThan(0, $d->compareTo(new Timestamp(2012, 5, 20, 0, 0, 0)));
    }

    /**
     * 以下を確認します.
     * 
     * - 内部フィールドが正常に変化すること
     * - 返り値がこのクラスのオブジェクトになること
     * 
     * @covers Peach\DT\TimeWrapper::add
     */
    public function testAdd()
    {
        $d1 = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $d2 = $d1->add("hour", 18);
        $this->assertEquals(new Timestamp(2012, 5, 22, 1, 30, 15), $d2->toTimestamp());
        $this->assertInstanceOf("Peach\\DT\\TimeWrapper", $d2);
    }

    /**
     * 以下を確認します.
     * 
     * - 内部フィールドが正常に変化すること
     * - 返り値がこのクラスのオブジェクトになること
     * 
     * @covers Peach\DT\TimeWrapper::set
     */
    public function testSet()
    {
        $d1 = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $d2 = $d1->set("min", 80);
        $this->assertEquals(new Timestamp(2012, 5, 21, 8, 20, 15), $d2->toTimestamp());
        $this->assertInstanceOf("Peach\\DT\\TimeWrapper", $d2);
    }

    /**
     * 以下を確認します.
     * 
     * - 内部フィールドが正常に変化すること
     * - 返り値がこのクラスのオブジェクトになること
     * 
     * @covers Peach\DT\TimeWrapper::setAll
     */
    public function testSetAll()
    {
        $d1 = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $d2 = $d1->setAll(array("year" => 2013, "month" => -1, "date" => 32, "sec" => 87));
        $this->assertEquals(new Timestamp(2012, 12, 2, 7, 31, 27), $d2->toTimestamp());
        $this->assertInstanceOf("Peach\\DT\\TimeWrapper", $d2);
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::get
     */
    public function testGet()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame(21, $d->get("date"));
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::format
     */
    public function testFormat()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame("2012-05-21 07:30:15", $d->format());
        $this->assertSame("2012-05-21T07:30:15", $d->format(W3cDatetimeFormat::getInstance()));
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::formatTime
     */
    public function testFormatTime()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame("07:30:15", $d->formatTime());
    }

    /**
     * 以下を確認します.
     * 
     * - クラスが異なる場合は FALSE
     * - クラスが同じで, フィールドの比較結果が 0 の場合は TRUE
     * 
     * @covers Peach\DT\TimeWrapper::equals
     */
    public function testEquals()
    {
        $d1 = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $d2 = new Timestamp(2012, 5, 21, 7, 30, 15);
        $d3 = new TimeWrapper(new Timestamp(2012, 1, 1, 0, 0, 0));
        $d4 = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertFalse($d1->equals($d2));
        $this->assertFalse($d1->equals($d3));
        $this->assertTrue($d1->equals($d4));
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::getDateCount
     */
    public function testGetDateCount()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame(31, $d->getDateCount());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::getDay
     */
    public function testGetDay()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame(1, $d->getDay());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::isLeapYear
     */
    public function testIsLeapYear()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertTrue($d->isLeapYear());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::toDate
     */
    public function testToDate()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertEquals(new Date(2012, 5, 21), $d->toDate());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::toDatetime
     */
    public function testToDatetime()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertEquals(new Datetime(2012, 5, 21, 7, 30), $d->toDatetime());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::toTimestamp
     */
    public function testToTimestamp()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertEquals(new Timestamp(2012, 5, 21, 7, 30, 15), $d->toTimestamp());
    }

    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\DT\TimeWrapper::__toString
     */
    public function test__toString()
    {
        $d = new TimeWrapper(new Timestamp(2012, 5, 21, 7, 30, 15));
        $this->assertSame("2012-05-21 07:30:15", $d->__toString());
    }
}
