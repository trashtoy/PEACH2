<?php
namespace Peach\Http;

use Peach\Http\Header\Raw;

class RequestTest extends \PHPUnit_Framework_TestCase
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
        $this->assertNull($obj->getHeader("Useragent"));
        $obj->setHeader($item);
        $this->assertSame($item, $obj->getHeader("Useragent"));
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
     * @covers Peach\Http\Request::getHeaderList
     * @todo   Implement testGetHeaderList().
     */
    public function testGetHeaderList()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    /**
     * @covers Peach\Http\Request::isMalformed
     * @todo   Implement testIsMalformed().
     */
    public function testIsMalformed()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
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
