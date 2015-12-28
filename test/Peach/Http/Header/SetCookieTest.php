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
     * "key=value" 形式の文字列を返すことを確認します.
     * もしも Cookie のキーまたは値が ASCII ではない場合,
     * URL エンコードに変換されます.
     * 
     * @covers Peach\Http\Header\SetCookie::__construct
     * @covers Peach\Http\Header\SetCookie::format
     */
    public function testFormat()
    {
        $obj1 = new SetCookie("foo", "test01");
        $this->assertSame("foo=test01", $obj1->format());
        $obj2 = new SetCookie("テスト", "確認");
        $this->assertSame("%E3%83%86%E3%82%B9%E3%83%88=%E7%A2%BA%E8%AA%8D", $obj2->format());
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
     * この Set-Cookie の値を返します.
     * 
     * @covers Peach\Http\Header\SetCookie::getValue
     */
    public function testGetValue()
    {
        $obj = new SetCookie("foo", "test01");
        $this->assertSame("test01", $obj->getValue());
    }
}
