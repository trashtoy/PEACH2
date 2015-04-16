<?php
namespace Peach\DT;

class SimpleFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var array
     */
    private $sampleList;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ($this->sampleList === null) {
            $d1 = new Date(2012, 5, 21);
            $d2 = new Datetime(2012, 5, 21, 7, 30);
            $d3 = new Timestamp(2012, 5, 21, 7, 30, 9);
            $d4 = new Date(2012, 3, 7);
            $d5 = new Datetime(2012, 3, 7, 0, 0);
            $d6 = new Timestamp(2012, 3, 7, 0, 0, 0);
            $d7 = Date::now();
            $d8 = Datetime::now()->setAll(array("hour" => 8, "minute" => 6));
            $d9 = Timestamp::now()->setAll(array("hour" => 8, "minute" => 6, "second" => 4));
            $this->sampleList = array(
                new SimpleFormatTest_Sample("YmdHis",
                    new SimpleFormatTest_ParseData("20120521073009", "2012052112345", $d1, $d2, $d3),
                    new SimpleFormatTest_FormatData($d1, "20120521000000"),
                    new SimpleFormatTest_FormatData($d2, "20120521073000"),
                    new SimpleFormatTest_FormatData($d3, "20120521073009")
                ),
                new SimpleFormatTest_Sample("Y年n月j日G時f分b秒",
                    new SimpleFormatTest_ParseData("2012年5月21日7時30分9秒", "2012年05月21日07時30分09秒", $d1, $d2, $d3),
                    new SimpleFormatTest_FormatData($d1, "2012年5月21日0時0分0秒"),
                    new SimpleFormatTest_FormatData($d2, "2012年5月21日7時30分0秒"),
                    new SimpleFormatTest_FormatData($d3, "2012年5月21日7時30分9秒")
                ),
                new SimpleFormatTest_Sample("\\Y=Y \\n=n \\j=j \\H=H \\i=i \\s=s",
                    new SimpleFormatTest_ParseData("Y=2012 n=5 j=21 H=07 i=30 s=09", "Y=2012 n=5 j=21 H=7 i=30 s=9", $d1, $d2, $d3),
                    new SimpleFormatTest_FormatData($d1, "Y=2012 n=5 j=21 H=00 i=00 s=00"),
                    new SimpleFormatTest_FormatData($d2, "Y=2012 n=5 j=21 H=07 i=30 s=00"),
                    new SimpleFormatTest_FormatData($d3, "Y=2012 n=5 j=21 H=07 i=30 s=09")
                ),
                new SimpleFormatTest_Sample("Y/m/d",
                    new SimpleFormatTest_ParseData("2012/03/07", "2012-03-07", $d4, $d5, $d6),
                    new SimpleFormatTest_FormatData($d1, "2012/05/21"),
                    new SimpleFormatTest_FormatData($d2, "2012/05/21"),
                    new SimpleFormatTest_FormatData($d3, "2012/05/21")
                ),
                new SimpleFormatTest_Sample("Y.n.j",
                    new SimpleFormatTest_ParseData("2012.3.7",   "hogehoge",   $d4, $d5, $d6),
                    new SimpleFormatTest_FormatData($d1, "2012.5.21"),
                    new SimpleFormatTest_FormatData($d2, "2012.5.21"),
                    new SimpleFormatTest_FormatData($d3, "2012.5.21")
                ),
                new SimpleFormatTest_Sample("H:i:s",
                    new SimpleFormatTest_ParseData("08:06:04",   "8:06:04",    $d7, $d8, $d9),
                    new SimpleFormatTest_FormatData($d1, "00:00:00"),
                    new SimpleFormatTest_FormatData($d2, "07:30:00"),
                    new SimpleFormatTest_FormatData($d3, "07:30:09")
                ),
                new SimpleFormatTest_Sample("G時f分b秒",
                    new SimpleFormatTest_ParseData("8時6分4秒",  "8じ6分4秒",  $d7, $d8, $d9),
                    new SimpleFormatTest_FormatData($d1, "0時0分0秒"),
                    new SimpleFormatTest_FormatData($d2, "7時30分0秒"),
                    new SimpleFormatTest_FormatData($d3, "7時30分9秒")
                ),
            );
        }
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの引数に指定した値を返すことを確認します.
     * @covers Peach\DT\SimpleFormat::getFormat
     * @covers Peach\DT\SimpleFormat::__construct
     * @covers Peach\DT\SimpleFormat::createContext
     */
    public function testGetFormat()
    {
        $arg = "\\Y=Y \\n=n \\j=j \\H=H \\i=i \\s=s";
        $f = new SimpleFormat($arg);
        $this->assertSame($arg, $f->getFormat());
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::parseDate
     * @covers Peach\DT\SimpleFormat::interpret
     * @covers Peach\DT\SimpleFormat\Numbers::match
     * @covers Peach\DT\SimpleFormat\Numbers::apply
     * @covers Peach\DT\SimpleFormat\Raw::__construct
     * @covers Peach\DT\SimpleFormat\Raw::match
     * @covers Peach\DT\SimpleFormat\Raw::apply
     * @covers Peach\DT\SimpleFormat::throwFormatException
     */
    public function testParseDate()
    {
        foreach ($this->sampleList as $sample) {
            $object   = $sample->getFormat();
            $valid    = $sample->getValidText();
            $invalid  = $sample->getInvalidText();
            $expected = $sample->getExpected("parseDate");
            $this->assertEquals($expected, $object->parseDate($valid));
            try {
                $object->parseDate($invalid);
                $this->fail();
            } catch (\Exception $e) {
                $this->assertInstanceOf("InvalidArgumentException", $e);
            }
        }
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::parseDatetime
     * @covers Peach\DT\SimpleFormat::interpret
     * @covers Peach\DT\SimpleFormat\Numbers::match
     * @covers Peach\DT\SimpleFormat\Numbers::apply
     * @covers Peach\DT\SimpleFormat\Raw::__construct
     * @covers Peach\DT\SimpleFormat\Raw::match
     * @covers Peach\DT\SimpleFormat\Raw::apply
     * @covers Peach\DT\SimpleFormat::throwFormatException
     */
    public function testParseDatetime()
    {
        foreach ($this->sampleList as $sample) {
            $object   = $sample->getFormat();
            $valid    = $sample->getValidText();
            $invalid  = $sample->getInvalidText();
            $expected = $sample->getExpected("parseDatetime");
            $this->assertEquals($expected, $object->parseDatetime($valid));
            try {
                $object->parseDatetime($invalid);
                $this->fail();
            } catch (\Exception $e) {
                $this->assertInstanceOf("InvalidArgumentException", $e);
            }
        }
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::parseTimestamp
     * @covers Peach\DT\SimpleFormat::interpret
     * @covers Peach\DT\SimpleFormat\Numbers::match
     * @covers Peach\DT\SimpleFormat\Numbers::apply
     * @covers Peach\DT\SimpleFormat\Raw::__construct
     * @covers Peach\DT\SimpleFormat\Raw::match
     * @covers Peach\DT\SimpleFormat\Raw::apply
     * @covers Peach\DT\SimpleFormat::throwFormatException
     */
    public function testParseTimestamp()
    {
        foreach ($this->sampleList as $sample) {
            $object   = $sample->getFormat();
            $valid    = $sample->getValidText();
            $invalid  = $sample->getInvalidText();
            $expected = $sample->getExpected("parseTimestamp");
            $this->assertEquals($expected, $object->parseTimestamp($valid));
            try {
                $object->parseTimestamp($invalid);
                $this->fail();
            } catch (\Exception $e) {
                $this->assertInstanceOf("InvalidArgumentException", $e);
            }
        }
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::formatDate
     * @covers Peach\DT\SimpleFormat::formatKey
     */
    public function testFormatDate()
    {
        foreach ($this->sampleList as $sample) {
            $format     = $sample->getFormat();
            $formatData = $sample->getFormatData("date");
            $expected   = $formatData->getExpected();
            $subject    = $formatData->getTime();
            $this->assertSame($expected, $format->formatDate($subject));
        }
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::formatDatetime
     * @covers Peach\DT\SimpleFormat::formatKey
     */
    public function testFormatDatetime()
    {
        foreach ($this->sampleList as $sample) {
            $format     = $sample->getFormat();
            $formatData = $sample->getFormatData("datetime");
            $expected   = $formatData->getExpected();
            $subject    = $formatData->getTime();
            $this->assertSame($expected, $format->formatDatetime($subject));
        }
    }
    
    /**
     * @covers Peach\DT\SimpleFormat::formatTimestamp
     * @covers Peach\DT\SimpleFormat::formatKey
     */
    public function testFormatTimestamp()
    {
        foreach ($this->sampleList as $sample) {
            $format     = $sample->getFormat();
            $formatData = $sample->getFormatData("timestamp");
            $expected   = $formatData->getExpected();
            $subject    = $formatData->getTime();
            $this->assertSame($expected, $format->formatTimestamp($subject));
        }
    }
}

/**
 * テストデータをあらわすクラスです
 */
class SimpleFormatTest_Sample
{
    /**
     * @var SimpleFormat
     */
    private $format;
    
    /**
     * @var SimpleFormatTest_ParseData
     */
    private $parseData;
    
    /**
     * @var array
     */
    private $formatDataList;
    
    /**
     * @param string                  $format
     * @param SimpleFormat_ParseData  $parseData
     * @param SimpleFormat_FormatData $fDate
     * @param SimpleFormat_FormatData $fDatetime
     * @param SimpleFormat_FormatData $fTimestamp
     */
    public function __construct($format,
        SimpleFormatTest_ParseData  $parseData,
        SimpleFormatTest_FormatData $fDate,
        SimpleFormatTest_FormatData $fDatetime,
        SimpleFormatTest_FormatData $fTimestamp)
    {
        $this->format    = new SimpleFormat($format);
        $this->parseData = $parseData;
        $this->formatDataList = array(
            "date"      => $fDate,
            "datetime"  => $fDatetime,
            "timestamp" => $fTimestamp,
        );
    }
    
    /**
     * テスト対象の SimpleFormat です
     * @return SimpleFormat
     */
    public function getFormat()
    {
        return $this->format;
    }
    
    /**
     * 妥当なフォーマットです
     * @return string
     */
    public function getValidText()
    {
        return $this->parseData->getValidText();
    }
    
    /**
     * 例外をスローするフォーマットです
     * @return string
     */
    public function getInvalidText()
    {
        return $this->parseData->getInvalidText();
    }
    
    /**
     * @param  string $type
     * @return Time
     */
    public function getExpected($type)
    {
        return $this->parseData->getExpected($type);
    }
    
    /**
     * @param  string $type
     * @return SimpleFormat_FormatData
     */
    public function getFormatData($type)
    {
        return $this->formatDataList[$type];
    }
}

class SimpleFormatTest_ParseData
{
    /**
     * @var string
     */
    private $validText;
    
    /**
     * @var string
     */
    private $invalidText;
    
    /**
     * @var array
     */
    private $expected;
    
    /**
     * @param string $validText
     * @param string $invalidText
     * @param Date   $date
     */
    public function __construct($validText, $invalidText, Date $date, Datetime $datetime, Timestamp $timestamp)
    {
        $this->validText   = $validText;
        $this->invalidText = $invalidText;
        $this->expected    = array(
            "parseDate"      => $date,
            "parseDatetime"  => $datetime,
            "parseTimestamp" => $timestamp,
        );
    }
    
    /**
     * 妥当なフォーマットです
     * @return string
     */
    public function getValidText()
    {
        return $this->validText;
    }
    
    /**
     * 例外をスローするフォーマットです
     * @return string
     */
    public function getInvalidText()
    {
        return $this->invalidText;
    }
    
    /**
     * @param  string $type
     * @return Time
     */
    public function getExpected($type)
    {
        return $this->expected[$type];
    }
}

class SimpleFormatTest_FormatData
{
    /**
     * @var Time
     */
    private $time;
    
    /**
     * @var string
     */
    private $expected;
    
    /**
     * @param Time   $time
     * @param string $expected
     */
    public function __construct(Time $time, $expected)
    {
        $this->time     = $time;
        $this->expected = $expected;
    }
    
    /**
     * @return Time
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * @return string
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
