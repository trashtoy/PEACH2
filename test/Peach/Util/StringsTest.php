<?php
namespace Peach\Util;

class StringsTest extends \PHPUnit_Framework_TestCase
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
     * explode() をテストします. 以下を確認します.
     * 
     * - オジリナルの explode() と同様の動作をすること
     * - 第 1 引数が空文字列の場合に空配列を返すこと
     * - 第 2 引数が文字列以外の値だった場合は stringValue() の結果が適用されること
     * 
     * @covers Peach\Util\Strings::explode
     */
    public function testExplode()
    {
        $this->assertSame(array("A", "B", "C"), Strings::explode("-", "A-B-C"));
        $this->assertSame(array(), Strings::explode("", "A-B-C"));
        $obj = new StringsTest_Object("A-B-C");
        $this->assertSame(array("A", "B", "C"), Strings::explode("-", $obj));
    }
    
    /**
     * 文字列が "\r", "\n", "\r\n" で分割されることを確認します.
     * @covers Peach\Util\Strings::getLines
     */
    public function testGetLines()
    {
        $str = "This\ris\na\r\npen.";
        $exp = array("This", "is", "a", "pen.");
        $this->assertSame($exp, Strings::getLines($str));
    }
    
    /**
     * isWhitespace() をテストします. 以下を確認します.
     * 
     * - 空文字列, null, false の場合 true を返す
     * - 半角スペース, TAB, 改行文字のみで構成された文字列の場合 true を返す
     * - 0 を含む数値型の値の場合 false を返す
     * - その他の文字が 1 文字以上含まれる場合 false を返す
     * 
     * @covers Peach\Util\Strings::isWhitespace
     */
    public function testIsWhitespace()
    {
        $this->assertSame(true,  Strings::isWhitespace(""));
        $this->assertSame(true,  Strings::isWhitespace(null));
        $this->assertSame(true,  Strings::isWhitespace(false));
        $this->assertSame(false, Strings::isWhitespace(true));
        $this->assertSame(false, Strings::isWhitespace(0));
        $this->assertSame(true,  Strings::isWhitespace("    \t\r\n    "));
        $this->assertSame(false, Strings::isWhitespace("  asdf "));
    }
    
    /**
     * basedir() をテストします. 以下を確認します.
     * 
     * - 引数が "/" 以外の文字列で終了している場合は末尾に "/" を付けた文字列を返す
     * - 引数が "/" で終了している場合は引数をそのまま返す
     * - 引数が空文字列の場合は空文字列を返す
     * - 引数が文字列型でない場合は文字列型に変換してから適用する
     * 
     * @covers Peach\Util\Strings::basedir
     */
    public function testBasedir()
    {
        $this->assertSame("/foo/bar/baz/", Strings::basedir("/foo/bar/baz"));
        $this->assertSame("/hoge/fuga/",   Strings::basedir("/hoge/fuga/"));
        $this->assertSame("",              Strings::basedir(""));
        $this->assertSame("asdf/",         Strings::basedir("asdf"));
        $obj = new StringsTest_Object("/aaa/bbb/ccc");
        $this->assertSame("/aaa/bbb/ccc/", Strings::basedir($obj));
    }
    
    /**
     * getRawIndex() をテストします. 以下を確認します.
     * 
     * - 第 1 引数の中で第 2 引数の文字が出現する位置を返す
     * - ヒットした文字が "\" でエスケープされている場合は無視する
     * - 見つからない場合は false を返す
     * 
     * @covers Peach\Util\Strings::getRawIndex
     */
    public function testGetRawIndex()
    {
        $this->assertSame(3,     Strings::getRawIndex("abc=def", "="));
        $this->assertSame(7,     Strings::getRawIndex("a\\=b\\=c=d", "="));
        $this->assertSame(false, Strings::getRawIndex("", "="));
        $this->assertSame(false, Strings::getRawIndex("a\\=b", "="));
        $this->assertSame(1,     Strings::getRawIndex("a=\\=b", "="));
    }
    
    /**
     * startsWith() をテストします. 以下を確認します.
     * 
     * - 第 1 引数の文字列の先頭が第 2 引数の文字列に合致した場合に true, それ以外は false を返す
     * - 第 2 引数が空文字列の場合は true を返す
     * - 引数が文字列型でない場合は文字列に変換してから適用する
     * 
     * @covers Peach\Util\Strings::startsWith
     */
    public function testStartsWith()
    {
        $this->assertSame(true,  Strings::startsWith("The quick brown fox", "The"));
        $this->assertSame(false, Strings::startsWith("Hogehoge", "hoge"));
        $this->assertSame(true,  Strings::startsWith("something", ""));
        $prefix  = new StringsTest_Object("TEST");
        $subject = new StringsTest_Object("TEST object");
        $other   = new StringsTest_Object("fuga");
        $this->assertSame(true,  Strings::startsWith($subject, $prefix));
        $this->assertSame(false, Strings::startsWith($subject, $other));
    }
    
    /**
     * endsWith() をテストします. 以下を確認します.
     * 
     * - 第 1 引数の文字列の末尾が第 2 引数の文字列に合致した場合に true, それ以外は false を返す
     * - 第 2 引数が空文字列の場合は true を返す
     * - 引数が文字列型でない場合は文字列に変換してから適用する
     * 
     * @covers Peach\Util\Strings::endsWith
     */
    public function testEndsWith()
    {
        $this->assertSame(true,  Strings::endsWith("The quick brown fox", "fox"));
        $this->assertSame(false, Strings::endsWith("Hogehoge", "Hoge"));
        $this->assertSame(true,  Strings::endsWith("something", ""));
        $suffix  = new StringsTest_Object("TEST");
        $subject = new StringsTest_Object("objectTEST");
        $other   = new StringsTest_Object("fuga");
        $this->assertSame(true,  Strings::endsWith($subject, $suffix));
        $this->assertSame(false, Strings::endsWith($subject, $other));
    }
    
    /**
     * endsWithRawChar() をテストします. 以下を確認します.
     * 
     * - 第 1 引数の文字列の末尾が第 2 引数の文字列に合致した場合に true, それ以外は false を返す
     * - ヒットした文字列が "\" でエスケープされている場合は false を返す
     * 
     * @covers Peach\Util\Strings::endsWithRawChar
     */
    public function testEndsWithRawChar()
    {
        $this->assertSame(true,  Strings::endsWithRawChar("ABCDE",       "DE"));
        $this->assertSame(true,  Strings::endsWithRawChar("AB\\CDE",     "DE"));
        $this->assertSame(false, Strings::endsWithRawChar("ABC\\DE",     "DE"));
        $this->assertSame(true,  Strings::endsWithRawChar("ABC\\\\DE",   "DE"));
        $this->assertSame(false, Strings::endsWithRawChar("ABC\\\\\\DE", "DE"));
    }
    
    /**
     * template() をテストします. 以下を確認します.
     * 
     * - 第 2 引数に空の配列を指定した場合は第 1 引数をそのまま返すこと
     * - 第 2 引数に指定した配列で第 1 引数のテンプレートが置換されること
     * 
     * @covers Peach\Util\Strings::template
     */
    public function testTemplate()
    {
        $exp1  = "hoge";
        $test1 = "hoge";
        $arr1  = array();
        $this->assertSame($exp1, Strings::template($test1, $arr1));
        
        $exp2  = "I am Tom, 12 years old.";
        $test2 = "I am {0}, {1} years old.";
        $arr2  = array("Tom", 12);
        $this->assertSame($exp2, Strings::template($test2, $arr2));
        
        $exp3  = "I am {1}.";
        $test3 = "I am {1}.";
        $arr3  = array("John");
        $this->assertSame($exp3, Strings::template($test3, $arr3));
        
        $exp4  = "First:{1},Second:{0}";
        $test4 = "First:{0},Second:{1}";
        $arr4  = array("{1}", "{0}");
        $this->assertSame($exp4, Strings::template($test4, $arr4));
        
        $exp5  = "I am John, 20 years old.";
        $test5 = "I am {name}, {age} years old.";
        $arr5 = array("name" => "John", "age" => 20);
        $this->assertSame($exp5, Strings::template($test5, $arr5));
    }
}

class StringsTest_Object
{
    private $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function __toString()
    {
        return $this->value;
    }
}
