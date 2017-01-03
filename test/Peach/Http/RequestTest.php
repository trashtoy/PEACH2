<?php
namespace Peach\Http;

use Peach\DT\Timestamp;
use Peach\Http\Header\HttpDate;
use Peach\Http\Header\NoField;
use Peach\Http\Header\QualityValues;
use Peach\Http\Header\Raw;
use PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Request();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * getPath() と setPath() のテストです. 以下を確認します.
     * 
     * - 初期状態では getPath() が null を返すこと
     * - setPath() で設定した値が getPath() から取得できること
     * 
     * @covers Peach\Http\Request::getPath
     * @covers Peach\Http\Request::setPath
     */
    public function testAccessPath()
    {
        $obj  = $this->object;
        $path = "/foo/bar/baz";
        $this->assertNull($obj->getPath());
        $obj->setPath($path);
        $this->assertSame($path, $obj->getPath());
    }
    
    /**
     * getHeader() と setHeader() のテストです. 以下を確認します.
     * 
     * - getHeader() の引数に存在しない名前を指定した場合 null を返すこと
     * - getHeader() が指定された名前に対応する HeaderField オブジェクトを返すこと
     * - setHeader() で設定した HeaderField オブジェクトが getHeader() から取得できること
     * 
     * @covers Peach\Http\Request::getHeader
     * @covers Peach\Http\Request::setHeader
     */
    public function testAccessHeader()
    {
        $obj  = $this->object;
        $item = new Raw("Useragent", "hogehoge");
        $this->assertSame(NoField::getInstance(), $obj->getHeader("Useragent"));
        $obj->setHeader($item);
        $this->assertSame($item, $obj->getHeader("Useragent"));
    }
    
    /**
     * getHeader() の第 1 引数について "Host" と ":authority" を同一視することを確認します.
     * 
     * @covers Peach\Http\Request::getHeader
     * @covers Peach\Http\Request::__construct
     */
    public function testAccessHost()
    {
       $obj1 = new Request();
       $h1   = new Raw(":authority", "www.example.com");
       $obj1->setHeader($h1);
       $this->assertSame($h1, $obj1->getHeader("Host"));
       $this->assertSame($h1, $obj1->getHeader(":authority"));
       
       $obj2 = new Request();
       $h2   = new Raw("Host", "www.example.com");
       $obj2->setHeader($h2);
       $this->assertSame($h2, $obj2->getHeader("Host"));
       $this->assertSame($h2, $obj2->getHeader(":authority"));
    }
    
    /**
     * getHeader() の引数が大文字・小文字を区別しないことを確認します.
     * 
     * @covers Peach\Http\Request::getHeader
     */
    public function testAccessHeaderByCaseInsensitive()
    {
        $obj  = $this->object;
        $item = new Raw("Useragent", "hogehoge");
        $obj->setHeader($item);
        $this->assertSame($item, $obj->getHeader("useragent"));
    }
    
    /**
     * 該当する HeaderField が存在する場合に true, 存在しない場合に false
     * を返すことを確認します.
     * 
     * @covers Peach\Http\Request::hasHeader
     */
    public function testHasHeader()
    {
        $obj  = $this->object;
        $item = new Raw("Connection", "keep-alive");
        $this->assertFalse($obj->hasHeader("Connection"));
        $obj->setHeader($item);
        $this->assertTrue($obj->hasHeader("Connection"));
    }
    
    /**
     * setHeader() で指定した HeaderField オブジェクトのすべてを配列として返すことを確認します.
     * 
     * @covers Peach\Http\Request::getHeaderList
     */
    public function testGetHeaderList()
    {
        $obj  = $this->object;
        $obj->setHeader(new Raw("Host", "www.example.com"));
        $obj->setHeader(new QualityValues("Accept-Language", array("ja" => 1.0, "en" => 0.5)));
        $obj->setHeader(new HttpDate("If-Modified-Since", new Timestamp(2012, 5, 21, 8, 34, 45)));
        
        $expected = array(
            ":authority"        => new Raw("Host", "www.example.com"),
            "accept-language"   => new QualityValues("Accept-Language", array("ja" => 1.0, "en" => 0.5)),
            "if-modified-since" => new HttpDate("If-Modified-Since", new Timestamp(2012, 5, 21, 8, 34, 45)),
        );
        $this->assertEquals($expected, $obj->getHeaderList());
    }
    
    /**
     * isMalformed() のテストです.
     * 以下の疑似ヘッダーがすべてセットされている場合のみ false を返すことを確認します.
     * 
     * - :path
     * - :authority
     * - :method
     * - :scheme
     * 
     * @param boolean $expected
     * @param Request $obj
     * @covers Peach\Http\Request::isMalformed
     * @dataProvider forTestIsMalformed
     */
    public function testIsMalformed($expected, Request $obj)
    {
        $this->assertSame($expected, $obj->isMalformed());
    }
    
    /**
     * testIsMalformed() のテストデータです.
     * 
     * @return array
     */
    public function forTestIsMalformed()
    {
        // OK
        $test1 = new Request();
        $test1->setHeader(new Raw(":path",      "/sample/index.html"));
        $test1->setHeader(new Raw(":authority", "www.example.com"));
        $test1->setHeader(new Raw(":method",    "get"));
        $test1->setHeader(new Raw(":scheme",    "http"));
        
        // :authority を Host に置換しても OK
        $test2 = new Request();
        $test2->setHeader(new Raw(":path", "/sample/index.html"));
        $test2->setHeader(new Raw("Host",  "www.example.com"));
        $test2->setHeader(new Raw(":method", "get"));
        $test2->setHeader(new Raw(":scheme", "http"));
        
        // :path がない
        $test3 = new Request();
        $test3->setHeader(new Raw("Host",  "www.example.com"));
        $test3->setHeader(new Raw(":method", "get"));
        $test3->setHeader(new Raw(":scheme", "http"));
        
        // :authority がない
        $test4 = new Request();
        $test4->setHeader(new Raw(":path", "/sample/index.html"));
        $test4->setHeader(new Raw(":method", "get"));
        $test4->setHeader(new Raw(":scheme", "http"));
        
        // :method がない
        $test5 = new Request();
        $test5->setHeader(new Raw(":path",      "/sample/index.html"));
        $test5->setHeader(new Raw(":authority", "www.example.com"));
        $test5->setHeader(new Raw(":scheme",    "http"));
        
        // :scheme がない
        $test6 = new Request();
        $test6->setHeader(new Raw(":path",      "/sample/index.html"));
        $test6->setHeader(new Raw(":authority", "www.example.com"));
        $test6->setHeader(new Raw(":method",    "get"));
        
        return array(
            array(false, $test1),
            array(false, $test2),
            array(true , $test3),
            array(true , $test4),
            array(true , $test5),
            array(true , $test6),
        );
    }
    
    /**
     * @covers Peach\Http\Request::setQuery
     * @covers Peach\Http\Request::getQuery
     */
    public function testAccessQuery()
    {
        $obj = $this->object;
        $this->assertNull($obj->getQuery("hoge"));
        $this->assertSame("asdf", $obj->getQuery("fuga", "asdf"));
        
        $params = array("hoge" => "1", "fuga" => 2, "piyo" => "xxxxx");
        $obj->setQuery($params);
        $this->assertSame("1", $obj->getQuery("hoge"));
        $this->assertSame(2, $obj->getQuery("fuga", "asdf"));
    }
    
    /**
     * @covers Peach\Http\Request::setPost
     * @covers Peach\Http\Request::getPost
     */
    public function testAccessPost()
    {
        $obj = $this->object;
        $this->assertNull($obj->getPost("hoge"));
        $this->assertSame("asdf", $obj->getPost("fuga", "asdf"));
        
        $params = array("hoge" => "1", "fuga" => 2, "piyo" => "xxxxx");
        $obj->setPost($params);
        $this->assertSame("1", $obj->getPost("hoge"));
        $this->assertSame(2, $obj->getPost("fuga", "asdf"));
    }
}
