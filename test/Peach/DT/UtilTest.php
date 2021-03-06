<?php
namespace Peach\DT;

class UtilTest extends \PHPUnit_Framework_TestCase
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
     * @return array
     */
    private function getTestArray()
    {
        static $d = null;
        if ($d === null) {
            $d = array(
                new Date     (2012, 3,  29),
                new Datetime (2012, 3,  29, 21, 59),
                new Timestamp(2012, 3,  29, 21, 59, 59),
                new Date     (2012, 5,  21),
                new Datetime (2012, 5,  21,  7, 30),
                new Timestamp(2012, 5,  21,  7, 30, 15),
                new Date     (2012, 10,  5),
                new Datetime (2012, 10,  5, 19,  0),
                new Timestamp(2012, 10,  5, 19,  0, 30),
            );
        }
        return $d;
    }
    
    /**
     * compareTime のテストです. 以下を確認します.
     * 
     * - 正しく比較が行えること
     * - 異なる型の比較で共通フィールドが全て等しかった場合, より上位の型を大とする
     * 
     * @covers Peach\DT\Util::compareTime
     */
    public function testCompareTime()
    {
        $d1 = $this->getTestArray();
        
        // 同じオブジェクト同士の比較は 0 を返します
        for ($i = 0; $i < 9; $i++) {
            $this->assertSame(0, Util::compareTime($d1[$i], $d1[$i]));
        }
        // 同じ型同士の比較が正しく出来ることを確認します
        for ($i = 0; $i < 3; $i ++) {
            $this->assertLessThan(0, Util::compareTime($d1[$i],     $d1[$i + 3]));
            $this->assertLessThan(0, Util::compareTime($d1[$i + 3], $d1[$i + 6]));
            $this->assertGreaterThan(0, Util::compareTime($d1[$i + 6], $d1[$i]));
        }
        // 異なる型同士で正しく比較できることを確認します
        // 共通フィールドすべてが等しい場合は、より多くのフィールドを持つほうが大となります
        for ($i = 0; $i < 8; $i ++) {
            $this->assertLessThan(0, Util::compareTime($d1[$i], $d1[$i + 1]));
            $this->assertGreaterThan(0, Util::compareTime($d1[$i + 1], $d1[$i]));
        }
        
        /*
         * ラッパーオブジェクトを含めたテスト
         */
        $obj1 = new Date(2012, 5, 21);
        $obj2 = new Datetime(2012, 5, 21, 0, 0);
        $obj3 = new Timestamp(2012, 5, 21, 0, 0, 0);
        $d2   = array(
            $obj1,
            $obj2,
            $obj3,
            new TimeWrapper($obj1),
            new TimeWrapper($obj2),
            new TimeWrapper($obj3),
        );
        $zeroTest1 = array(0, 1, 2, 3, 4, 5);
        $zeroTest2 = array(3, 4, 5, 0, 1, 2);
        $subTest1  = array(1, 2, 2, 4, 5, 5);
        $subTest2  = array(0, 0, 1, 3, 3, 4);
        $subTest3  = array(3, 3, 4, 0, 0, 1);
        
        for ($i = 0; $i < 6; $i ++) {
            $i1 = $zeroTest1[$i];
            $i2 = $zeroTest2[$i];
            $i3 = $subTest1[$i];
            $i4 = $subTest2[$i];
            $i5 = $subTest3[$i];
            
            // あるオブジェクトと、そのオブジェクトのラッパーオブジェクトの比較は 0 を返します
            $this->assertSame(0, Util::compareTime($d2[$i1], $d2[$i2]));
            $this->assertSame(0, Util::compareTime($d2[$i2], $d2[$i1]));
            
            // 共通フィールドがすべて等しい場合、より多くのフィールドを持つほうが大となります
            $this->assertGreaterThan(0, Util::compareTime($d2[$i3], $d2[$i4]));
            $this->assertGreaterThan(0, Util::compareTime($d2[$i3], $d2[$i5]));
            $this->assertLessThan(0, Util::compareTime($d2[$i4], $d2[$i3]));
            $this->assertLessThan(0, Util::compareTime($d2[$i5], $d2[$i3]));
        }
    }
    
    /**
     * 以下を確認します.
     * 
     * - 配列を引数にして実行できること
     * - 引数を羅列して実行できること
     * - 引数に不正な型を含む場合, その値が無視されること
     * 
     * @covers Peach\DT\Util::oldest
     */
    public function testOldest()
    {
        $d = $this->getTestArray();
        $this->assertSame($d[0], Util::oldest($d));
        $this->assertSame($d[1], Util::oldest($d[4], $d[1], $d[3], $d[5], $d[8]));
        $this->assertSame($d[2], Util::oldest($d[5], null, $d[2], 128, $d[3]));
    }
    
    /**
     * 以下を確認します.
     * 
     * - 配列を引数にして実行できること
     * - 引数を羅列して実行できること
     * - 引数に不正な型を含む場合, その値が無視されること
     * 
     * @covers Peach\DT\Util::latest
     */
    public function testLatest()
    {
        $d = $this->getTestArray();
        $this->assertSame($d[8], Util::latest($d));
        $this->assertSame($d[6], Util::latest($d[4], $d[2], $d[6], $d[0], $d[5]));
        $this->assertSame($d[7], Util::latest($d[1], 256, $d[7], false, $d[3]));
    }
    
    /**
     * 年・月・日・時・分・秒 のそれぞれについて,
     * 妥当な場合と妥当でない場合の返り値を確認します.
     * 
     * 引数に文字列が含まれていた場合は, 数字文字列 (is_numeric() が TRUE を返す)
     * の場合のみ OK とします.
     * 
     * @covers Peach\DT\Util::validate
     */
    public function testValidate()
    {
        $this->assertTrue(Util::validate(2012, 2, 29));
        $this->assertTrue(Util::validate(2012, 5, 21, 18, 30));
        $this->assertTrue(Util::validate(2012, 3,  1, 23,  0, 30));
        $this->assertTrue(Util::validate(2012, 5, 21,  7, 30, "1"));
        $this->assertFalse(Util::validate(2011, 2, 29));
        $this->assertFalse(Util::validate(2012, -1, 1));
        $this->assertFalse(Util::validate(2012, 5, 21, 25, 0, 30));
        $this->assertFalse(Util::validate(2012, 5, 21, 1, 0, -1));
        $this->assertFalse(Util::validate(2012, 5, 21, 7, 30, "hoge"));
    }
    
    /**
     * システムの時差を分単位で取得することを確認します.
     * @covers Peach\DT\Util::getTimeZoneOffset
     */
    public function testGetTimeZoneOffset()
    {
        $this->assertSame(-540, Util::getTimeZoneOffset());
    }
    
    /**
     * cleanTimeZoneOffset() のテストです. 以下を確認します.
     * 
     * - 基本的に, 指定された整数をそのまま返す.
     * - 1425 より大きい値 (-23:45 以前) は 1425 に丸める
     * - -1425 より小さい値 (+23:45 以降) は -1425 に丸める
     * - 数値以外の値は整数に変換する
     * 
     * @covers Peach\DT\Util::cleanTimeZoneOffset
     */
    public function testCleanTimeZoneOffset()
    {
        $expected = array(300, -540, 1425, -1425, 0);
        $test     = array(300, null, 1500, -1800, "asdf");
        for ($i = 0; $i < 5; $i ++) {
            $this->assertSame($expected[$i], Util::cleanTimeZoneOffset($test[$i]));
        }
    }
}
