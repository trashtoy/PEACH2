<?php
namespace Peach\DT;

class TimeEquatorTest extends \PHPUnit_Framework_TestCase
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
     * コンストラクタのテストです. コンストラクタの引数について以下を確認します.
     * 
     * - Time::TYPE_DATE と array("year", "month", "date") が同一視されること
     * - Time::TYPE_DATETIME と
     *   array("year", "month", "date", "hour", "minute")
     *   が同一視されること
     * - Time::TYPE_TIMESTAMP と
     *   array("year", "month", "date", "hour", "minute", "second")
     *   が同一視されること
     * - 文字列と array(文字列) が同一視されること
     * 
     * @covers Peach\DT\TimeEquator::__construct
     * @covers Peach\DT\TimeEquator::initFields
     */
    public function test__construct()
    {
        $this->assertEquals(
            new TimeEquator(array("year", "month", "date")),
            new TimeEquator(Time::TYPE_DATE)
        );
        $this->assertEquals(
            new TimeEquator(array("year", "month", "date", "hour", "minute")),
            new TimeEquator(Time::TYPE_DATETIME)
        );
        $this->assertEquals(
            new TimeEquator(array("year", "month", "date", "hour", "minute", "second")),
            new TimeEquator(Time::TYPE_TIMESTAMP)
        );
        $this->assertEquals(
            new TimeEquator(array("year")), new TimeEquator("year")
        );
    }
    
    /**
     * 引数なしのコンストラクタで生成したインスタンスと等価のオブジェクトを返すことを確認します.
     * @covers Peach\DT\TimeEquator::getDefault
     * @covers Peach\DT\TimeEquator::__construct
     * @covers Peach\DT\TimeEquator::initFields
     */
    public function testGetDefault()
    {
        $this->assertEquals(new TimeEquator(), TimeEquator::getDefault());
    }
    
    /**
     * デフォルトの TimeEquator の場合, {@link Time::equals()}
     * を使って比較を行うことを確認します.
     * 具体的には, 二つの時間オブジェクトの型が等しく,
     * すべてのフィールドが同じ場合のみ TRUE となります.
     * 
     * @covers Peach\DT\TimeEquator::equate
     */
    public function testEquate1()
    {
        $e = TimeEquator::getDefault();
        
        $d1 = new Date(2012, 5, 21);
        $d2 = new TimeWrapper($d1);
        $this->assertFalse($e->equate($d1, $d2));
        
        $d3 = new Date(2012, 5, 21);
        $this->assertTrue($e->equate($d1, $d3));
        
        $d4 = new Datetime(2012, 10, 31, 7, 30);
        $this->assertFalse($e->equate($d1, $d4));
    }
    
    /**
     * 引数を指定して初期化された TimeEquator が
     * 指定されたフィールドのみ比較することを確認します.
     * 
     * また, 未定義のフィールド (NULL) と 0 を明確に区別することも確認します.
     * array("hour", "minute", "second") で初期化された TimeEquator が
     * 0 時 0 分 0 秒の Timestamp オブジェクトと,
     * 時・分・秒が未定義である Date オブジェクトを比較した場合,
     * equate は FALSE を返します.
     * 
     * @covers Peach\DT\TimeEquator::equate
     */
    public function testEquate2()
    {
        $d1 = new Timestamp(2012,  5, 21, 7, 30, 45);
        $d2 = new Timestamp(2012, 12, 24, 7, 30, 45);
        $d3 = new Timestamp(2012,  5, 21, 7, 34, 56);
        $d4 = new Timestamp(2012,  5, 21, 0,  0,  0);
        $d5 = new Date(2012, 5, 21);
        
        $e1 = new TimeEquator(Time::TYPE_DATE);
        $this->assertFalse($e1->equate($d1, $d2));
        $this->assertTrue($e1->equate($d1, $d3));
        $this->assertTrue($e1->equate($d1, $d5));
        
        $e2 = new TimeEquator(array("hour", "minute", "second"));
        $this->assertTrue($e2->equate($d1, $d2));
        $this->assertFalse($e2->equate($d1, $d3));
        $this->assertFalse($e2->equate($d1, $d5));
        
        // 0 と null を区別するため FALSE を返す
        $this->assertFalse($e2->equate($d4, $d5));
    }
    
    /**
     * equate() の引数のうち Time オブジェクトでないものが
     * 1 つ以上ある場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\TimeEquator::equate
     */
    public function testEquateFail1()
    {
        $d1 = new Timestamp(2012,  5, 21, 7, 30, 45);
        $e  = new TimeEquator();
        $e->equate($d1, "foobar");
    }
    
    /**
     * equate() の引数のうち Time オブジェクトでないものが
     * 1 つ以上ある場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\TimeEquator::equate
     */
    public function testEquateFail2()
    {
        $d1 = new Timestamp(2012,  5, 21, 7, 30, 45);
        $e  = new TimeEquator();
        $e->equate("foobar", $d1);
    }
    
    /**
     * equate() の引数のうち Time オブジェクトでないものが
     * 1 つ以上ある場合に InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\TimeEquator::equate
     */
    public function testEquateFail3()
    {
        $e  = new TimeEquator();
        $e->equate("foo", "bar");
    }
    
    /**
     * Date, Datetime, Timestamp それぞれについて
     * 期待されたハッシュ値を算出していることを確認します.
     * 
     * @covers Peach\DT\TimeEquator::hashCode
     */
    public function testHashCode()
    {
        $d1 = new Date(2012, 5, 21);
        $d2 = new Datetime(2012, 5, 21, 7, 30);
        $d3 = new Timestamp(2012, 5, 21, 7, 30, 45);
        
        $e  = TimeEquator::getDefault();
        $this->assertSame(22348, $e->hashCode($d1));
        $this->assertSame(27936515, $e->hashCode($d2));
        $this->assertSame(1316248310, $e->hashCode($d3));
    }
    
    /**
     * Time オブジェクト以外の引数を指定した場合に
     * InvalidArgumentException をスローすることを確認します.
     * @expectedException InvalidArgumentException
     * @covers Peach\DT\TimeEquator::hashCode
     */
    public function testHashCodeFail()
    {
        $e  = TimeEquator::getDefault();
        $e->hashCode("asdf");
    }
}
