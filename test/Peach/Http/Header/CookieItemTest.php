<?php

namespace Peach\Http\Header;

use Peach\DT\Timestamp;
use PHPUnit_Framework_TestCase;

class CookieItemTest extends PHPUnit_Framework_TestCase
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
     * format() のテストです. 以下を確認します.
     * 
     * - 初期化時に CookieOption を指定しない場合は "key=value" 形式の文字列を返すこと
     * - 初期化時に CookieOption を指定した場合は属性を付与した文字列を返すこと
     * - キーおよび値にマルチバイト文字列や記号などが含まれる場合は urlencode された文字列を返すこと
     * 
     * @covers Peach\Http\Header\CookieItem::__construct
     * @covers Peach\Http\Header\CookieItem::format
     * @covers Peach\Http\Header\CookieItem::formatData
     */
    public function testFormat()
    {
        $obj1 = new CookieItem("foobar", "asdf");
        $this->assertSame("foobar=asdf", $obj1->format());
        
        $opt  = new CookieOptions();
        $opt->setTimeZoneOffset(-540);
        $opt->setExpires(new Timestamp(2009, 2, 14, 8, 31, 30));
        $opt->setMaxAge(3600);
        $opt->setDomain("example.com");
        $opt->setPath("/foo/bar");
        $opt->setSecure(true);
        $opt->setHttpOnly(true);
        $obj2 = new CookieItem("test", 10, $opt);
        $exp2 = "test=10; expires=Fri, 13-Feb-2009 23:31:30 GMT; max-age=3600; domain=example.com; path=/foo/bar; secure; httponly";
        $this->assertSame($exp2, $obj2->format());
        
        $obj3 = new CookieItem("テスト", "<TEST VALUE>");
        $this->assertSame("%E3%83%86%E3%82%B9%E3%83%88=%3CTEST%20VALUE%3E", $obj3->format());
    }

}
