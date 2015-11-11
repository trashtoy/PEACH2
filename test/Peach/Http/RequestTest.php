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
}
