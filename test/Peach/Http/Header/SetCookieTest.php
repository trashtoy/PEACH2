<?php
namespace Peach\Http\Header;

use PHPUnit_Framework_TestCase;

class SetCookieTest extends PHPUnit_Framework_TestCase
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
     * コンストラクタのテストです. 以下を確認します.
     * 
     * - 引数を指定しない場合, cookie を何も持たないインスタンスが生成されること
     * - 引数を指定した場合, 指定された引数に応じた cookie が 1 つ追加された状態となること
     * 
     * @covers Peach\Http\Header\SetCookie::__construct
     * @covers Peach\Http\Header\SetCookie::format
     */
    public function test__construct()
    {
        $obj1 = new SetCookie();
        $this->assertSame(array(), $obj1->format());
        
        $obj2 = new SetCookie("foo", "test01");
        $this->assertSame(array("foo=test01"), $obj2->format());
        
        $opt  = new CookieOptions();
        $opt->setPath("/test");
        $opt->setMaxAge(86400);
        $obj3 = new SetCookie("bar", "test02", $opt);
        $this->assertSame(array("bar=test02; max-age=86400; path=/test"), $obj3->format());
    }
    
    /**
     * "key=value" 形式の文字列を返すことを確認します.
     * もしも Cookie のキーまたは値が ASCII ではない場合,
     * URL エンコードに変換されます.
     * 
     * @covers Peach\Http\Header\SetCookie::__construct
     * @covers Peach\Http\Header\SetCookie::setItem
     * @covers Peach\Http\Header\SetCookie::format
     */
    public function testFormat()
    {
        $expected1 = array("foo=test01", "bar=test02");
        $obj1      = new SetCookie("foo", "test01");
        $obj1->setItem("bar", "test02");
        $this->assertSame($expected1, $obj1->format());
        
        $expected2 = array("%E3%83%86%E3%82%B9%E3%83%88=%E7%A2%BA%E8%AA%8D");
        $obj2 = new SetCookie("テスト", "確認");
        $this->assertSame($expected2, $obj2->format());
    }
    
    /**
     * 文字列 "set-cookie" を返すことを確認します.
     * 
     * @covers Peach\Http\Header\SetCookie::getName
     */
    public function testGetName()
    {
        $obj = new SetCookie("foo", "test01");
        $this->assertSame("set-cookie", $obj->getName());
    }
    
    /**
     * 対象の SetCookie オブジェクトにセットされている CookieItem の配列を返すことを確認します.
     * 
     * @covers Peach\Http\Header\SetCookie::getValues
     * @covers Peach\Http\Header\SetCookie::setItem
     */
    public function testGetValues()
    {
        $expected = array(
            new CookieItem("foo", "test01"),
            new CookieItem("bar", "test02"),
        );
        $obj      = new SetCookie("foo", "test01");
        $obj->setItem("bar", "test02");
        $this->assertEquals($expected, $obj->getValues());
    }
}
