<?php
namespace Peach\Http;

use PHPUnit_Framework_TestCase;
use Peach\Http\Header\Raw;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Response();
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
     * - getHeader() が指定された名前に対応する HeaderItem オブジェクトを返すこと
     * - setHeader() で設定した HeaderItem オブジェクトが getHeader() から取得できること
     * 
     * @covers Peach\Http\Response::getHeader
     * @covers Peach\Http\Response::setHeader
     */
    public function testAccessHeader()
    {
        $obj  = $this->object;
        $item = new Raw("Server", "Apache");
        $this->assertNull($obj->getHeader("Server"));
        $obj->setHeader($item);
        $this->assertSame($item, $obj->getHeader("Server"));
    }
    
    /**
     * getHeader() の引数が大文字・小文字を区別しないことを確認します.
     * 
     * @covers Peach\Http\Response::getHeader
     */
    public function testAccessHeaderByCaseInsensitive()
    {
        $obj  = $this->object;
        $item = new Raw("X-Powered-By", "PHP/5.6.0");
        $obj->setHeader($item);
        $this->assertSame($item, $obj->getHeader("x-powered-by"));
    }
    
    /**
     * 該当する HeaderItem が存在する場合に true, 存在しない場合に false
     * を返すことを確認します.
     * 
     * @covers Peach\Http\Response::hasHeader
     */
    public function testHasHeader()
    {
        $obj  = $this->object;
        $item = new Raw("Accept-Ranges", "bytes");
        $this->assertFalse($obj->hasHeader("Accept-Ranges"));
        $obj->setHeader($item);
        $this->assertTrue($obj->hasHeader("Accept-Ranges"));
    }
}
