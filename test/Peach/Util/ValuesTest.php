<?php
namespace Peach\Util;

class ValuesTest extends \PHPUnit_Framework_TestCase
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
     * intValue() のテストです.
     * 以下を確認します.
     * 
     * - intval() のアルゴリズムに基づいて値を整数に変換する
     * - 最小値・最大値が指定されていない場合は, 変換結果を返す
     * - 最小値が指定されており, 変換結果が最小値より小さい場合は最小値を返す
     * - 最大値が指定されており, 変換結果が最大値より大きい場合は最大値を返す
     * - 最小値より最大値のほうが小さい場合, 最大値の指定は無視する
     * 
     * @covers Peach\Util\Values::intValue
     */
    public function testIntValue()
    {
        // intval() に基づいて値を整数に変換する
        $this->assertSame(10, Values::intValue(10));
        $this->assertSame(3,  Values::intValue(3.14));
        $this->assertSame(-2, Values::intValue(-2.71));
        $this->assertSame(-1, Values::intValue("-1asdf"));
        $this->assertSame(0,  Values::intValue("hoge"));
        $this->assertSame(1,  Values::intValue(true));
        $this->assertSame(0,  Values::intValue(false));
        $this->assertSame(0,  Values::intValue(null));
        $this->assertSame(1,  Values::intValue(new \stdClass()));
        $this->assertSame(0,  Values::intValue(array()));
        $this->assertSame(1,  Values::intValue(array(1, 2, 3)));
        
        // 第一引数が最小値 (10) より小さい値は最小値を返す
        $this->assertSame(11, Values::intValue(11, 10));
        $this->assertSame(10, Values::intValue(10, 10));
        $this->assertSame(10, Values::intValue(9,  10));
        
        // 第一引数が最大値 (90) より大きな値は最大値を返す
        $this->assertSame(90, Values::intValue(91, null, 90));
        $this->assertSame(90, Values::intValue(90, null, 90));
        $this->assertSame(89, Values::intValue(89, null, 90));
        $this->assertSame(-5, Values::intValue(-5, null, 90));
        
        // 最小値と最大値の両方が指定されている場合
        $this->assertSame(10, Values::intValue(5,  10, 90));
        $this->assertSame(50, Values::intValue(50, 10, 90));
        $this->assertSame(90, Values::intValue(95, 10, 90));
        
        // 最大値 (10) が最小値 (50) より小さい場合, 最大値の指定を無視する
        $this->assertSame(50, Values::intValue(49, 50, 10));
        $this->assertSame(50, Values::intValue(50, 50, 10));
        $this->assertSame(51, Values::intValue(51, 50, 10));
    }
    
    /**
     * stringValue() のテストです.
     * 以下を確認します.
     * 
     * - __toString() が定義されているオブジェクトは, __toString() の結果を返す.
     * - __toString() が定義されていないオブジェクトは, クラス名を返す.
     * - スカラー値は strval() のアルゴリズムに基づいて文字列に変換する.
     * - リソース型の値は "resource_type #num" 形式の文字列となる. (例えば "stream #1")
     * 
     * @covers Peach\Util\Values::stringValue
     */
    public function testStringValue()
    {
        $obj = new ValuesTest_Object("asdf");
        $std = new \stdClass();
        $fp  = fopen(__FILE__, "r");
        
        // __toString() が定義されているオブジェクトは、呼び出した結果を返す
        $this->assertSame("test value=asdf", Values::stringValue($obj));
        
        // __toString() が定義されていないオブジェクトはクラス名を返す
        $this->assertSame("stdClass",        Values::stringValue($std));
        
        // スカラー値は strval() のアルゴリズムに基づく
        $this->assertSame("hoge",            Values::stringValue("hoge"));
        $this->assertSame("",                Values::stringValue(null));
        $this->assertSame("1",               Values::stringValue(true));
        $this->assertSame("0",               Values::stringValue(0));
        
        // リソース型は "resource_type #num" 形式の文字列
        $this->assertStringStartsWith("stream #", Values::stringValue($fp));
    }
    
    /**
     * arrayValue() のテストです.
     * 以下を確認します.
     * 
     * - 引数が配列の場合, $force の指定によらず引数をそのまま返す
     * - 引数が配列以外の場合, $force == TRUE の場合は引数を長さ 1 の配列として返す
     * - 引数が配列以外の場合, $force == FALSE の場合は空の配列を返す
     * 
     * @covers Peach\Util\Values::arrayValue
     */
    public function testArrayValue()
    {
        // 引数が配列の場合, ($force の指定によらず) 引数をそのまま返す
        $this->assertSame(array(1),      Values::arrayValue(array(1)));
        $this->assertSame(array(2),      Values::arrayValue(array(2), true));
        
        // 配列以外の値を指定した場合, $force == FALSE の場合は空の配列を返す
        $this->assertSame(array(),       Values::arrayValue("hoge"));
        $this->assertSame(array(),       Values::arrayValue("hoge", false));
        
        // 配列以外の値を指定した場合, $force == TRUE の場合は長さ 1 の配列にして返す
        $this->assertSame(array("hoge"), Values::arrayValue("hoge", true));
    }
    
    /**
     * boolValue() をテストします.
     * 以下を確認します.
     * 
     * - "T", "Y", "O" (大小問わず) で始まる文字列は true を返す.
     * - "F", "N" (大小問わず) で始まる文字列は false を返す.
     * 
     * @covers Peach\Util\Values::boolValue
     * @covers Peach\Util\Values::handleBoolValue
     * @covers Peach\Util\Values::stringToBool
     */
    public function testBoolValue()
    {
        // "T", "Y", "O" で始まる文字列と 0 以外の数値, TRUE は常に TRUE を返す
        $okList = array("test", "True", "yes", "Young", "orz", "OK", true, 1.5, -10);
        foreach ($okList as $value) {
            $this->assertTrue(Values::boolValue($value));
            $this->assertTrue(Values::boolValue($value, true));
            $this->assertTrue(Values::boolValue($value, false));
        }
        
        // "F", "N" で始まる文字列と 0, FALSE は常に FALSE を返す
        $ngList = array("false", "FOX", "NG", "no", false, 0, 0.0);
        foreach ($ngList as $value) {
            $this->assertFalse(Values::boolValue($value));
            $this->assertFalse(Values::boolValue($value, true));
            $this->assertFalse(Values::boolValue($value, false));
        }
        
        // それ以外の文字列, 型の場合は $defaultValue に応じて返り値が決まる
        // $defaultValue が未指定の場合はキャストした結果となる
        $castTrue = array(
            "ABC",
            array(0, 0, 0), // サイズが 1 以上の配列は TRUE
            new \stdClass()
        );
        foreach ($castTrue as $value) {
            $this->assertTrue(Values::boolValue($value));
            $this->assertTrue(Values::boolValue($value, true));
            $this->assertFalse(Values::boolValue($value, false));
        }
        
        $castFalse = array(
            "0",
            "",
            null,
            array()
        );
        foreach ($castFalse as $value) {
            $this->assertFalse(Values::boolValue($value));
            $this->assertTrue(Values::boolValue($value, true));
            $this->assertFalse(Values::boolValue($value, false));
        }
    }
    
    /**
     * getType() をテストします.
     * 以下を確認します.
     * 
     * - オブジェクト以外の引数については, gettype() と同じ結果になること
     * - オブジェクトについては, そのオブジェクトのクラス名を返すこと
     * 
     * @covers Peach\Util\Values::getType
     */
    public function testGetType()
    {
        $tests = array(
            array(123,           "integer"),
            array("asdf",        "string"),
            array(new \stdClass, "stdClass"),
        );
        
        foreach ($tests as $test) {
            $this->assertSame($test[1], Values::getType($test[0]));
        }
    }
}

class ValuesTest_Object
{
    private $value;
    
    public function __construct($value)
    {
        $this->value  = $value;
    }
    
    public function __toString()
    {
        return "test value=" . $this->value;
    }
}
