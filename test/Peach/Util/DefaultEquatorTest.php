<?php
namespace Peach\Util;

class DefaultEquatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultEquator
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = DefaultEquator::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * getInstance() をテストします. 以下を確認します.
     * 
     * - 返り値が DefaultEquator 型であること
     * - 何回実行しても同一のオブジェクトを返すこと
     * 
     * @covers Peach\Util\DefaultEquator::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = DefaultEquator::getInstance();
        $obj2 = DefaultEquator::getInstance();
        $this->assertInstanceOf("Peach\\Util\\DefaultEquator", $obj1);
        $this->assertTrue($obj1 === $obj2);
    }
    
    /**
     * equate() をテストします. 返り値が以下のようになることを確認します.
     * 
     * - 等価な整数と浮動小数点数 (-10 と -10.0 など) を比較した場合 true
     * - とある数値とその数値の文字列表現 (15 と "15" など) を比較した場合 true
     * - 「2a問題」("2a" と 2 など) の比較は false
     * - 論理値と数値 (true と 1 など) を比較した場合 false
     * - 等価なオブジェクト同士は true (そうでない場合 false)
     * - 同じキーと値から成り立つ配列同士は true (そうでない場合 false)
     * 
     * @covers Peach\Util\DefaultEquator::equate
     */
    public function testEquate()
    {
        $e    = $this->object;
        $obj1 = new DefaultEquatorTest_Object(15);
        $obj2 = new DefaultEquatorTest_Object(20);
        $obj3 = new DefaultEquatorTest_Object(15);
        $arr1 = array("a" => 10, "b" => 15, "c" => 13);
        $arr2 = array("a" => 10, "b" => 15, "c" => 12);
        $arr3 = array("a" => 10, "b" => 15, "c" => 12);
        $this->assertTrue($e->equate(-10, -10.0));
        $this->assertTrue($e->equate("15", 15));
        $this->assertFalse($e->equate("2a", 2));
        $this->assertFalse($e->equate(true, 1));
        $this->assertFalse($e->equate($obj1, $obj2));
        $this->assertTrue($e->equate($obj1,  $obj3));
        $this->assertFalse($e->equate($arr1, $arr2));
        $this->assertTrue($e->equate($arr2,  $arr3));
    }
    
    /**
     * hashCode() をテストします. 返り値が以下のようになることを確認します.
     * 
     * - null, false, 空の配列 は 0
     * - 数値の場合, その数値の整数部分の絶対値
     * - 等価な値 (またはオブジェクト) の場合は同じハッシュ値となること
     * 
     * @covers Peach\Util\DefaultEquator::hashCode
     */
    public function testHashCode()
    {
        $e = $this->object;
        $this->assertSame(0,  $e->hashCode(null));
        $this->assertSame(0,  $e->hashCode(false));
        $this->assertSame(0,  $e->hashCode(array()));
        $this->assertSame(10, $e->hashCode(10));
        $this->assertSame(20, $e->hashCode("0020"));
        $test1 = new DefaultEquatorTest_Object(123);
        $test2 = new DefaultEquatorTest_Object(123);
        $hash1 = $e->hashCode($test1);
        $hash2 = $e->hashCode($test2);
        $this->assertSame($hash1, $hash2);
    }
}

class DefaultEquatorTest_Object
{
    private $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
}
