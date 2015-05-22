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
     * 複雑な構造の JSON 文字列を該当する値に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     */
    public function testDecode()
    {
        $codec    = $this->object;
        $text     = file_get_contents(__DIR__ . "/JsonCodec/decode-ok.txt");
        $expected = array(
            "first" => array(
                "test01" => 0,
                "test02" => 100,
                "test03" => -50,
            ),
            "second" => array(1.0, -1.5, 12.5e-7),
            "third" => "hogehoge",
            "fourth" => array(
                "test04" => array(
                    "true" => true,
                    "false" => false,
                    "null" => null,
                ),
            ),
        );
        $this->assertSame($expected, $codec->decode($text));
    }
    
    /**
     * 不正な JSON 文字列を decode した際に適切なエラーメッセージを持つ
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Closing bracket after comma is not permitted at line 6, column 5
     */
    public function testDecodeFail()
    {
        $codec    = $this->object;
        $text     = file_get_contents(__DIR__ . "/JsonCodec/decode-ng.txt");
        $codec->decode($text);
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
     * 文字列を該当する値に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     * @covers Peach\DF\JsonCodec\Root::__construct
     * @covers Peach\DF\JsonCodec\Root::handle
     * @covers Peach\DF\JsonCodec\Root::getResult
     */
    public function testDecodeString()
    {
        $codec    = $this->object;
        $test     = '    "This\\ris\\na pen"    ';
        $expected = "This\ris\na pen";
        $this->assertSame($expected, $codec->decode($test));
    }
    
    /**
     * 数値表現を該当する値に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::decode
     */
    public function testDecodeNumber()
    {
        $codec    = $this->object;
        $test     = "    \n3.14e+16\n    ";
        $this->assertSame(3.14e+16, $codec->decode($test));
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
    
    /**
     * リテラル (null, true, false) を対応する文字列に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     */
    public function testEncodeLiteral()
    {
        $codec = $this->object;
        $this->assertSame("null",  $codec->encode(null));
        $this->assertSame("true",  $codec->encode(true));
        $this->assertSame("false", $codec->encode(false));
    }
    
    /**
     * 数値を対応する文字列に変換することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     */
    public function testEncodeNumber()
    {
        $codec = $this->object;
        $this->assertSame("10", $codec->encode(10));
        $this->assertSame("-5", $codec->encode(-5));
        $this->assertSame("1.75", $codec->encode(1.75));
        $this->assertSame("1.125E-9", $codec->encode(1.125e-9));
        $this->assertSame("-5.15625E+16", $codec->encode(-5.15625e16));
    }
}
