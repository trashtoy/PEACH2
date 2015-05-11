<?php
namespace Peach\DF;

class JsonCodecTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonCodec
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new JsonCodec();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * @covers Peach\DF\JsonCodec::decode
     * @todo   Implement testDecode().
     */
    public function testDecode()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    /**
     * リテラル (null, true, false) を該当する値に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     * @covers Peach\DF\JsonCodec\Root::__construct
     * @covers Peach\DF\JsonCodec\Root::handle
     * @covers Peach\DF\JsonCodec\Root::getResult
     * @covers Peach\DF\JsonCodec\WS::handle
     */
    public function testDecodeLiteral()
    {
        $codec = $this->object;
        $this->assertSame(null, $codec->decode("null"));
        $this->assertSame(true, $codec->decode("    true"));
        $this->assertSame(false, $codec->decode("    false    "));
    }
    
    /**
     * リテラルの decode に失敗した際に InvalidArgumentException
     * をスローすることを確認します.
     * 
     * @covers Peach\DF\JsonCodec\Value::decodeLiteral
     * @expectedException \InvalidArgumentException
     */
    public function testDecodeLiteralFail()
    {
        $codec = $this->object;
        $codec->decode("   testfail   ");
    }
      
    /**
     * 空文字列を decode した場合に null を返すことを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     */
    public function testDecodeEmptyString()
    {
        $codec       = $this->object;
        $emptyValues = array("", "    ", "\r\n\t");
        foreach ($emptyValues as $e) {
            $this->assertNull($codec->decode($e));
        }
    }
    
    /**
     * JSON の末尾に余計な値が続いていた場合はエラーとなることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     * @covers Peach\DF\JsonCodec\Root::handle
     * @expectedException \InvalidArgumentException
     */
    public function testDecodeFailByInvalidSuffix()
    {
        $codec = $this->object;
        $codec->decode("    true   \nfalse   ");
    }
    
    /**
     * @covers Peach\DF\JsonCodec::encode
     * @todo   Implement testEncode().
     */
    public function testEncode()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
