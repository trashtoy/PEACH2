<?php
namespace Peach\DT;
require_once(__DIR__ . "/AbstractTimeTest.php");
use Peach\Util\ArrayMap;

class DatetimeTest extends AbstractTimeTest
{
    /**
     * @var string
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
     * オブジェクトの各フィールドが現在時刻のそれに等しいかどうかを調べます.
     * このメソッドは, テストを開始するタイミングによって極稀に失敗する可能性があるため,
     * 失敗した場合は再度テストしてください.
     * 
     * @covers Peach\DT\Datetime::now
     */
    public function testNow()
    {
        $d    = Datetime::now();
        $time = time();
        $this->assertSame(intval(date("Y", $time)), $d->get("year"));
        $this->assertSame(intval(date("n", $time)), $d->get("month"));
        $this->assertSame(intval(date("j", $time)), $d->get("date"));
        $this->assertSame(intval(date("G", $time)), $d->get("hour"));
        $this->assertSame(intval(date("i", $time)), $d->get("min"));
    }
    
    /**
     * 任意の Clock オブジェクトを引数に指定して now() を実行した場合,
     * その Clock があらわす現在時刻の Datetime オブジェクトを返すことを確認します.
     * 
     * @covers Peach\DT\Datetime::now
     */
    public function testNowByClock()
    {
        $clock = new FixedClock(1234567890);
        $d     = Datetime::now($clock);
        $this->assertSame(2009, $d->get("year"));
        $this->assertSame(2,    $d->get("month"));
        $this->assertSame(14,   $d->get("date"));
        $this->assertSame(8,    $d->get("hour"));
        $this->assertSame(31,   $d->get("minute"));
    }
    
    /**
     * parse に成功した場合に Datetime オブジェクト を返すことを確認します.
     * 
     * @covers Peach\DT\Datetime::parse
     */
    public function testParse()
    {
        $d = Datetime::parse("2011-05-21 07:30");
        $this->assertEquals(new Datetime(2011, 5, 21, 7, 30), $d);
    }
    
    /**
     * parse に失敗した場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\Datetime::parse
     */
    public function testParseFail()
    {
        Datetime::parse("Illegal Format");
    }
    
    /**
     * {@link Time::TYPE_DATETIME} を返すことを確認します.
     * @covers Peach\DT\Datetime::getType
     */
    public function testGetType()
    {
        $d = new Datetime(2012, 5, 21, 7, 30);
        $this->assertSame(Time::TYPE_DATETIME, $d->getType());
    }
    
    /**
     * "hh:mm" 形式の文字列を返すことを確認します.
     * @covers Peach\DT\Datetime::formatTime
     */
    public function testFormatTime()
    {
        $d = new Datetime(2012, 5, 21, 7, 30);
        $this->assertSame("07:30", $d->formatTime());
    }
    
    /**
     * 形式が "YYYY-MM-DD hh:mm" 形式になっていることを確認します.
     * @covers Peach\DT\Datetime::__toString
     */
    public function test__toString()
    {
        $t = new Datetime(2012, 5, 21, 7, 30);
        $this->assertSame("2012-05-21 07:30", $t->__toString());
    }
    
    /**
     * Datetime から Date へのキャストをテストします.
     * @covers Peach\DT\Datetime::toDate
     */
    public function testToDate()
    {
        $d1 = new Datetime(2012, 5, 21, 0, 0);
        $this->assertEquals(new Date(2012, 5, 21), $d1->toDate());
    }
    
    /**
     * Datetime から Datetime へのキャストをテストします.
     * 生成されたオブジェクトが, 元のオブジェクトのクローンであることを確認します.
     * すなわち, == による比較が TRUE, === による比較が FALSE となります.
     * @covers Peach\DT\Datetime::toDatetime
     */
    public function testToDatetime()
    {
        $t1 = new Datetime(2012, 5, 21, 7, 30);
        $t2 = $t1->toDatetime();
        $this->assertEquals($t1, $t2);
        $this->assertNotSame($t1, $t2);
    }
    
    /**
     * Datetime から Timestamp へのキャストをテストします.
     * 生成されたオブジェクトについて, 以下の点を確認します.
     * 
     * - 年・月・日・時・分のフィールドが元のオブジェクトのものと等しい
     * - 秒のフィールドが 0 になっている
     * 
     * @covers Peach\DT\Datetime::toTimestamp
     */
    public function testToTimestamp()
    {
        $test = new Datetime(2012, 5, 21, 7, 30);
        $this->assertEquals(new Timestamp(2012, 5, 21, 7, 30, 0), $test->toTimestamp());
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - フィールドの加減が正常に出来ること.
     * - 不正なフィールド名を指定した場合に無視されること.
     */
    public function testAdd()
    {
        $d1 = new Datetime(2012, 5, 21, 7, 30);
        $this->assertEquals(new Datetime(2015, 5,  21,  7, 30),  $d1->add("year",   3));
        $this->assertEquals(new Datetime(2009, 5,  21,  7, 30),  $d1->add("year",  -3));
        $this->assertEquals(new Datetime(2012, 10, 21,  7, 30),  $d1->add("month",  5));
        $this->assertEquals(new Datetime(2011, 12, 21,  7, 30),  $d1->add("month", -5));
        $this->assertEquals(new Datetime(2012, 6,  10,  7, 30),  $d1->add("date",  20));
        $this->assertEquals(new Datetime(2012, 4,  21,  7, 30),  $d1->add("date", -30));
        $this->assertEquals(new Datetime(2012, 5,  21, 17, 30),  $d1->add("hour",  10));
        $this->assertEquals(new Datetime(2012, 5,  20, 21, 30),  $d1->add("hour", -10));
        $this->assertEquals(new Datetime(2012, 5,  21,  8, 15),  $d1->add("min",   45));
        $this->assertEquals(new Datetime(2012, 5,  21,  6, 45),  $d1->add("min ", -45));
        
        $this->assertEquals(new Datetime(2012, 5,  21,  7, 30),  $d1->add("sec",  -10));
        $this->assertEquals(new Datetime(2012, 5,  21,  7, 30),  $d1->add("asdf",  20));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 比較が正常に出来る
     * - 同じオブジェクトの場合は FALSE を返す
     * - 異なる型との比較で, 共通のフィールドが全て等しい場合は, フィールドが多いほうが「後」
     * 
     * @covers Peach\DT\Datetime::after
     */
    public function testAfter()
    {
        $d1 = new Datetime(2012, 5, 21, 7, 30);
        
        // 比較が正常にできる
        $this->assertTrue($d1->after(new Datetime(2012, 5, 21, 5, 59)));
        $this->assertFalse($d1->after(new Datetime(2012, 6, 6, 23, 0)));
        
        // 同じオブジェクトの場合は FALSE を返す
        $this->assertFalse($d1->after(new Datetime(2012, 5, 21, 7, 30)));
        
        // 異なる型との比較で, 共通のフィールドが全て等しい場合は, フィールドが多いほうが「後」
        $this->assertTrue($d1->after(new Date(2012, 5, 21)));
        $this->assertFalse($d1->after(new Timestamp(2012, 5, 21, 7, 30, 0)));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 比較が正常に出来る
     * - 同じオブジェクトの場合は FALSE を返す
     * - 異なる型との比較で, 共通のフィールドが全て等しい場合は, フィールドが少ないほうが「前」
     * - Time 以外のオブジェクトと比較した場合は FALSE を返す
     * 
     * @covers Peach\DT\Datetime::before
     */
    public function testBefore()
    {
        $d1 = new Datetime(2012, 5, 21, 7, 30);
        
        // 比較が正常にできる
        $this->assertFalse($d1->before(new Datetime(2011, 12, 31, 23, 59)));
        $this->assertTrue($d1->before(new Datetime(2012, 5, 21, 12, 0)));
        
        // 同じオブジェクトの場合は FALSE を返す
        $this->assertFalse($d1->before(new Datetime(2012, 5, 21, 7, 30)));
        
        // 異なる型との比較で, 共通のフィールドが全て等しい場合は, フィールドが少ないほうが「前」
        $this->assertFalse($d1->before(new Date(2012, 5, 21)));
        $this->assertTrue($d1->before(new Timestamp(2012, 5, 21, 7, 30, 0)));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 比較が正常に出来る
     * - 対象オブジェクトが Datetime を継承していない場合でも比較が出来る
     * - 引数に時間オブジェクト以外の値を指定した場合は null を返す
     * 
     * @covers Peach\DT\AbstractTime::compareTo
     * @covers Peach\DT\Datetime::compareFields
     */
    public function testCompareTo()
    {
        $d = array(
            new Datetime(2012, 3, 12, 23, 59),
            new Datetime(2012, 5, 21,  3, 15),
            new Datetime(2012, 5, 21,  7, 30),
            new Datetime(2012, 5, 21,  7, 45),
            new Datetime(2013, 1, 23,  1, 23),
        );
        $this->assertGreaterThan(0, $d[2]->compareTo($d[0]));
        $this->assertGreaterThan(0, $d[2]->compareTo($d[1]));
        $this->assertSame(0, $d[2]->compareTo($d[2]));
        $this->assertLessThan(0, $d[2]->compareTo($d[3]));
        $this->assertLessThan(0, $d[2]->compareTo($d[4]));

        $w1 = new TimeWrapper($d[2]);
        $w2 = $w1->add("hour", -3);
        $w3 = $w1->add("minute", 15);
        $this->assertGreaterThan(0, $d[2]->compareTo($w2));
        $this->assertLessThan(0, $d[2]->compareTo($w3));
        $this->assertSame(0, $d[2]->compareTo($w1));
        
        $this->assertNull($d[2]->compareTo("foobar"));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 同じ型で, 全てのフィールドの値が等しいオブジェクトの場合は TRUE
     * - 同じ型で, 一つ以上のフィールドの値が異なるオブジェクトの場合は FALSE
     * - 型が異なる場合は FALSE
     */
    public function testEquals()
    {
        $d1 = new Datetime(2012, 5, 21, 7, 30);
        $d2 = new Datetime(2012, 5, 21, 1, 23);
        $d3 = new Timestamp(2012, 5, 21, 7, 30, 0);
        $w  = new TimeWrapper($d1);
        $this->assertTrue($d1->equals($d1));
        $this->assertFalse($d1->equals($d2));
        $this->assertFalse($d1->equals($d3));
        $this->assertFalse($d1->equals($w));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 指定された Format オブジェクトの formatDatetime() メソッドを使って書式化されること
     * - 引数を省略した場合は __toString と同じ結果を返すこと
     */
    public function testFormat()
    {
        $d = new Datetime(2012, 5, 21, 7, 30);
        $this->assertSame("2012-05-21 07:30", $d->format());
        $this->assertSame("2012-05-21T07:30", $d->format(W3cDatetimeFormat::getInstance()));
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 年・月・日・時・分のフィールドの取得が出来る
     * - 不正な引数を指定した場合は NULL を返す
     */
    public function testGet()
    {
        $time        = new Datetime(2012, 5, 21, 7, 30);
        $valid       = array();
        $valid[7]    = array("h", "H", "HOUR", "hour", "heaven"); // any string which starts with "h" is OK.
        $valid[30]   = array("M", "m", "Min", "min", "mushroom"); // any string which starts with "m" (except "mo") is OK.
        $invalid = array("sec", null, "bar");
        foreach ($valid as $expected => $v) {
            foreach ($v as $key) {
                $this->assertEquals($time->get($key), $expected);
            }
        }
        foreach ($invalid as $key) {
            $this->assertNull($time->get($key));
        }
    }
    
    /**
     * 以下の確認を行います.
     * 
     * - 時・分のフィールドの設定が出来る
     * - 不正な引数を指定した場合は同じオブジェクトを返す
     */
    public function testSet()
    {
        $time = new Datetime(2012, 5, 21, 7, 30);
        $this->assertEquals(
            array(
                new Datetime(2012, 5, 21, 10, 30),
                new Datetime(2012, 5, 20, 23, 30),
                new Datetime(2012, 5, 22,  0, 30),
            ),
            array(
                $time->set("h", 10),
                $time->set("h", -1),
                $time->set("h", 24),
            )
        );
        $this->assertEquals(
            array(
                new Datetime(2012, 5, 21, 7, 45),
                new Datetime(2012, 5, 21, 8,  5),
                new Datetime(2012, 5, 21, 6, 55),
            ), 
            array(
                $time->set("m", 45),
                $time->set("m", 65),
                $time->set("m", -5),
            )
        );
        $this->assertEquals($time, $time->set("foobar", 10));
    }
    
    /**
     * 以下を確認します.
     * 
     * - 配列を引数にして日付の設定が出来ること
     * - Util_Map を引数にして日付の設定が出来ること
     * - 範囲外のフィールドが指定された場合に, 上位のフィールドから順に調整されること
     * 
     * @covers Peach\DT\Datetime::setAll
     */
    public function testSetAll()
    {
        $d     = new Datetime(2012, 5, 21, 7, 30);
        $test1 = $d->setAll(array("min" => 34, "hour" => 18));
        $this->assertEquals(new Datetime(2012, 5, 21, 18, 34), $test1);
        
        $map   = new ArrayMap();
        $map->put("mon", 10);
        $map->put("min", 55);
        $test2 = $d->setAll($map);
        $this->assertEquals(new Datetime(2012, 10, 21, 7, 55), $test2);
        
        // 2012-05-21T36:-72 => 2012-05-22T12:-72 => 2012-05-22T10:48
        $this->assertEquals(new Datetime(2012, 5, 22, 10, 48), $d->setAll(array("hour" => 36, "min" => -72)));
    }
    
    /**
     * 配列・Map 以外の型を指定した場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\Datetime::setAll
     */
    public function testSetAllFail()
    {
        $d = new Datetime(2012, 5, 21, 7, 30);
        $d->setAll("hoge");
    }
}
