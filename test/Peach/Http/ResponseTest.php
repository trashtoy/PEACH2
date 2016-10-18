<?php
namespace Peach\Http;

use Peach\Http\Body\StringRenderer;
use Peach\Http\Header\NoField;
use Peach\Http\Header\Raw;
use Peach\Http\Header\Status;
use Peach\Http\Response;
use PHPUnit_Framework_TestCase;

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
     * - getHeader() が指定された名前に対応する HeaderField オブジェクトを返すこと
     * - setHeader() で設定した HeaderField オブジェクトが getHeader() から取得できること
     * 
     * @covers Peach\Http\Response::getHeader
     * @covers Peach\Http\Response::setHeader
     */
    public function testAccessHeader()
    {
        $obj  = $this->object;
        $item = new Raw("Server", "Apache");
        $this->assertSame(NoField::getInstance(), $obj->getHeader("Server"));
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
     * 該当する HeaderField が存在する場合に true, 存在しない場合に false
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
    
    /**
     * isMalformed() が true を返す条件が正しいかどうかをテストします.
     * 以下の条件に一つ以上合致した場合に malformed とみなします.
     * 
     * - ステータスライン (:status 擬似ヘッダー) が存在しない
     * 
     * @covers Peach\Http\Response::isMalformed
     * @covers Peach\Http\Response::validateStatusHeader
     */
    public function testIsMalformed()
    {
        $obj1 = new Response();
        $this->assertTrue($obj1->isMalformed());
        $obj1->setHeader(new Status("404", "File Not Found"));
        $this->assertFalse($obj1->isMalformed());
        
        $obj2 = new Response();
        $obj2->setHeader(new Raw(":status", "This is invalid"));
        $this->assertTrue($obj2->isMalformed());
    }
    
    /**
     * getBody() と setBody() のテストです. 以下を確認します.
     * 
     * - setBody() で設定した Body オブジェクトが getBody() から取得できること
     * - setBody() が実行されていない状態で getBody() が null を返すこと
     * 
     * @covers Peach\Http\Response::setBody
     * @covers Peach\Http\Response::getBody
     */
    public function testAccessBody()
    {
        $obj1 = $this->object;
        $this->assertNull($obj1->getBody());
        $body = new Body("Hogehoge", StringRenderer::getInstance());
        $obj1->setBody($body);
        $this->assertSame($body, $obj1->getBody());
    }
}
