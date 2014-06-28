<?php
namespace Peach\Util;

class DefaultComparatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultComparator
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = DefaultComparator::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * {@link DefaultComparator::compare} をテストします.
     * 以下を確認します.
     * 
     * - 一般的な大小比較
     * - string("2a") > int(2) となること (2a問題をクリアしていること)
     * - オブジェクト同士の比較では, var_dump の結果で大小比較が行われること
     * - Comparable の実装オブジェクトの場合, compareTo の結果で代償比較が行われること
     * 
     * @covers Peach\Util\DefaultComparator::compare
     */
    public function testCompare()
    {
        $c = $this->object;
        $this->assertLessThan(   0, $c->compare(10,   20));
        $this->assertGreaterThan(0, $c->compare(25,   15.0));
        $this->assertSame(       0, $c->compare(10,   10));
        $this->assertSame(       0, $c->compare("3",  3));
        
        $this->assertGreaterThan(0, $c->compare("2a", 2));
        
        // Comparable を実装していないオブジェクトの場合
        // var_dump の結果が適用されるため, 13 と 5 は文字列として比較される
        $obj1 = new DefaultComparatorTest_Object(13, "hoge");
        $obj2 = new DefaultComparatorTest_Object(5,  "asdf");
        $obj3 = new DefaultComparatorTest_Object(5,  "fuga");
        $obj4 = new DefaultComparatorTest_Object(13, "hoge");
        $this->assertLessThan(   0, $c->compare($obj1, $obj2));
        $this->assertSame(       0, $c->compare($obj1, $obj4));
        $this->assertLessThan(   0, $c->compare($obj2, $obj3));
        $this->assertGreaterThan(0, $c->compare($obj3, $obj1));
        
        // Comparable を実装しているオブジェクトの場合
        // compareTo の結果が適用されるため, 80 と 120 は数値として比較される
        $c1   = new DefaultComparatorTest_C("Tom",  80);
        $c2   = new DefaultComparatorTest_C("Anna", 120);
        $c3   = new DefaultComparatorTest_C("John", 120);
        $c4   = new DefaultComparatorTest_C("Tom",  80);
        $this->assertLessThan(   0, $c->compare($c1, $c2));
        $this->assertLessThan(   0, $c->compare($c2, $c3));
        $this->assertSame(       0, $c->compare($c1, $c4));
        $this->assertGreaterThan(0, $c->compare($c3, $c1));
    }
    
    /**
     * {@link DefaultComparator::getInstance} のテストをします.
     * 以下を確認します.
     * 
     * - 返り値が DefaultComparator のインスタンスである
     * - どの返り値も, 同一のインスタンスを返す
     * 
     * @covers Peach\Util\DefaultComparator::getInstance
     */
    public function testGetInstance()
    {
        $c1 = DefaultComparator::getInstance();
        $c2 = DefaultComparator::getInstance();
        $this->assertInstanceOf("Peach\\Util\\DefaultComparator", $c1);
        $this->assertSame($c1, $c2);
    }
}

class DefaultComparatorTest_Object
{
    private $id;
    private $name;
    
    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}

class DefaultComparatorTest_C implements Comparable
{
    private $name;
    private $value;
    
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }
    
    public function compareTo($subject)
    {
        if ($this->value !== $subject->value) {
            return $this->value - $subject->value;
        }
        
        return strcmp($this->name, $subject->name);
    }
}
