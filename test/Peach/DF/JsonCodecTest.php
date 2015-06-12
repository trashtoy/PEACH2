<?php
namespace Peach\DF;
use Peach\Util\Strings;

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
     * getEncodeOption() のテストです.
     * 引数に指定した配列に応じた結果を返すことを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::initOptions
     * @covers Peach\DF\JsonCodec::getEncodeOption
     */
    public function testGetOption()
    {
        $c1  = new JsonCodec();
        $this->assertFalse($c1->getEncodeOption(JsonCodec::UNESCAPED_SLASHES));
        $this->assertFalse($c1->getEncodeOption(JsonCodec::UNESCAPED_UNICODE));
        
        $opt = array(
            JsonCodec::UNESCAPED_SLASHES => true,
            JsonCodec::UNESCAPED_UNICODE => true,
        );
        $c2  = new JsonCodec($opt);
        $this->assertTrue($c2->getEncodeOption(JsonCodec::UNESCAPED_SLASHES));
        $this->assertTrue($c2->getEncodeOption(JsonCodec::UNESCAPED_UNICODE));
    }
    
    /**
     * コンストラクタに指定するオプションにビットマスクによる整数を指定した場合,
     * 配列で指定した場合と同じオプション設定となることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::initOptions
     * @covers Peach\DF\JsonCodec::initOptionsByBitMask
     * @covers Peach\DF\JsonCodec::getEncodeOption
     */
    public function test__constructByBitMask()
    {
        $this->assertEquals(new JsonCodec(), new JsonCodec(0));
        $opt1 = array(
            JsonCodec::PRESERVE_ZERO_FRACTION => false,
            JsonCodec::HEX_TAG => true,
            JsonCodec::HEX_QUOT => false,
            JsonCodec::NUMERIC_CHECK => true,
            JsonCodec::UNESCAPED_UNICODE => true,
        );
        $opt2 = JsonCodec::HEX_TAG | JsonCodec::NUMERIC_CHECK | JsonCodec::UNESCAPED_UNICODE;
        $obj1 = new JsonCodec($opt1);
        $obj2 = new JsonCodec($opt2);
        for ($i = 1; $i <= 1024; $i <<= 1) {
            $this->assertSame($obj1->getEncodeOption($i), $obj2->getEncodeOption($i));
        }
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
     * @covers Peach\DF\JsonCodec::encodeFloat
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
    
    /**
     * オプション NUMERIC_CHECK を指定した場合の encode のテストです. 以下を確認します.
     * 
     * - オプションが有効な場合は数値表現の文字列の場合を数値としてエンコードすること
     * - 有効でない場合は数値表現の文字列をそのまま文字列としてエンコードすること
     * 
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeNumeric
     */
    public function testEncodeNumberWithNumericCheck()
    {
        $obj1 = $this->object;
        $obj2 = new JsonCodec(array(JsonCodec::NUMERIC_CHECK => true));
        $this->assertSame('"-123"', $obj1->encode('-123'));
        $this->assertSame('-123', $obj2->encode('-123'));
        $this->assertSame('"1.25E+20"', $obj1->encode('1.25E+20'));
        $this->assertSame('1.25E+20', $obj2->encode('1.25E+20'));
    }
    
    /**
     * オプション PRESERVE_ZERO_FRACTION を指定した場合の encode のテストです. 以下を確認します.
     * 
     * - オプションが有効な場合は 2.0 のような float 型の値をそのまま float 値としてエンコードすること
     * - 有効でない場合は 2.0 のような float 型の値を整数としてエンコードすること
     * 
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeFloat
     */
    public function testEncodeNumberWithPreserveZeroFraction()
    {
        $obj1 = $this->object;
        $obj2 = new JsonCodec(array(JsonCodec::PRESERVE_ZERO_FRACTION => true));
        $this->assertSame("2", $obj1->encode(2.0));
        $this->assertSame("2.0", $obj2->encode(2.0));
        $this->assertSame("1.25E+18", $obj2->encode(1.25e18));
    }
    
    /**
     * 文字列の encode のテストです.
     * json_encode をオプションなしで実行した結果と同じ内容となることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeString
     * @covers Peach\DF\JsonCodec::encodeCodePoint
     */
    public function testEncodeString()
    {
        $codec = $this->object;
        
        $test1 = "";
        for ($i = 0; $i < 127; $i++) {
            $test1 .= chr($i);
        }
        $expected1 =
                '"\u0000\u0001\u0002\u0003\u0004\u0005\u0006\u0007\b\t\n\u000b\f\r\u000e\u000f' .
                '\u0010\u0011\u0012\u0013\u0014\u0015\u0016\u0017\u0018\u0019\u001a\u001b\u001c\u001d\u001e\u001f' .
                ' !\"#$%&' . "'" . '()*+,-.\/0123456789:;<=>?' .
                '@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\\\]^_`abcdefghijklmnopqrstuvwxyz{|}~"';
        $this->assertSame($expected1, $codec->encode($test1));
        
        $test2     = implode("", array_map("chr", array(0xE3, 0x83, 0x86, 0xE3, 0x82, 0xB9, 0xE3, 0x83, 0x88))); // "テスト"
        $expected2 = '"\u30c6\u30b9\u30c8"';
        $this->assertSame($expected2, $codec->encode($test2));
    }
    
    /**
     * オプション UNESCAPED_SLASHES を指定した場合, "/" がエスケープされずに encode されることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeString
     */
    public function testEncodeStringWithUnescapedSlashes()
    {
        $obj1 = $this->object;
        $obj2 = new JsonCodec(array(JsonCodec::UNESCAPED_SLASHES => true));
        $this->assertSame("\"\\/foo\\/bar\\/baz\"", $obj1->encode("/foo/bar/baz"));
        $this->assertSame("\"/foo/bar/baz\"", $obj2->encode("/foo/bar/baz"));
    }
    
    /**
     * オプション HEX_TAG, HEX_AMP, HEX_APOS, HEX_QUOT のテストです.
     * 対応する文字をエスケープしてエンコードすることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeString
     * @covers Peach\DF\JsonCodec::encodeCodePoint
     */
    public function testEncodeStringWithHexOptions()
    {
        $options = array(
            JsonCodec::HEX_TAG  => true,
            JsonCodec::HEX_AMP  => true,
            JsonCodec::HEX_APOS => true,
            JsonCodec::HEX_QUOT => true,
        );
        $obj1      = $this->object;
        $obj2      = new JsonCodec($options);
        $test      = "<a href=\"/\">It's a test&heart;</a>";
        $expected1 = '"<a href=\"\/\">It\'s a test&heart;<\/a>"';
        $expected2 = '"\u003Ca href=\u0022\/\u0022\u003EIt\u0027s a test\u0026heart;\u003C\/a\u003E"';
        $this->assertSame($expected1, $obj1->encode($test));
        $this->assertSame($expected2, $obj2->encode($test));
    }
    
    /**
     * オプション UNESCAPED_UNICODE を指定した場合, マルチバイト文字がエスケープされずに
     * UTF-8 文字で符号化された状態で encode されることを確認します.
     */
    public function testEncodeStringWithUnescapedUnicode()
    {
        $obj1 = $this->object;
        $obj2 = new JsonCodec(array(JsonCodec::UNESCAPED_UNICODE => true));
        $test = implode("", array_map("chr", array(0xE3, 0x83, 0x86, 0xE3, 0x82, 0xB9, 0xE3, 0x83, 0x88))); // "テスト"
        $this->assertSame('"\u30c6\u30b9\u30c8"', $obj1->encode($test));
        $this->assertSame("\"{$test}\"", $obj2->encode($test));
    }
    
    /**
     * 配列の encode のテストです.
     * 配列のキーの内容に応じて以下のような結果となることを確認します.
     * 
     * - キーが 0, 1, 2, 3... という具合に 0 から始まる整数の連続になっていた場合は配列
     * - それ以外はオブジェクト
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeArray
     * @covers Peach\DF\JsonCodec::encodeObject
     * @covers Peach\DF\JsonCodec::checkKeySequence
     */
    public function testEncodeArray()
    {
        $codec     = $this->object;
        $test1     = array("foo" => 123, "bar" => true, "baz" => "asdf");
        $expected1 = '{"foo":123,"bar":true,"baz":"asdf"}';
        $this->assertSame($expected1, $codec->encode($test1));
        
        $test2     = array(0 => "aaaa", 1 => "bbbb", 2 => "cccc", 3 => "dddd");
        $expected2 = '["aaaa","bbbb","cccc","dddd"]';
        $this->assertSame($expected2, $codec->encode($test2));
        
        $test3     = array(0 => "aaaa", 1 => "bbbb", 3 => "cccc", 2 => "dddd");
        $expected3 = '{"0":"aaaa","1":"bbbb","3":"cccc","2":"dddd"}';
        $this->assertSame($expected3, $codec->encode($test3));
        
        $test4     = array(0 => "aaaa", 1 => "bbbb", 2 => "cccc", 4 => "dddd");
        $expected4 = '{"0":"aaaa","1":"bbbb","2":"cccc","4":"dddd"}';
        $this->assertSame($expected4, $codec->encode($test4));
    }
    
    /**
     * オブジェクトの encode のテストです.
     * 指定されたオブジェクトの public メンバ変数が,
     * 対応するキーおよび値に変換されることを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeObject
     */
    public function testEncodeObject()
    {
        $codec = $this->object;
        $test1 = new \stdClass();
        $test2 = new \stdClass();
        $test2->first  = "hoge";
        $test2->second = "fuga";
        $test2->third  = "piyo";
        $test1->obj    = $test2;
        $test1->val    = 150;
        
        $expected = '{"obj":{"first":"hoge","second":"fuga","third":"piyo"},"val":150}';
        $this->assertSame($expected, $codec->encode($test1));
    }
    
    /**
     * オプション PRETTY_PRINT を指定した場合,
     * object および array 形式の値を半角スペース 4 個と改行文字で整形して出力することを確認します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     * @covers Peach\DF\JsonCodec::encodeArray
     * @covers Peach\DF\JsonCodec::encodeObject
     */
    public function testEncodeWithPrettyPrint()
    {
        $obj1 = $this->object;
        $obj2 = new JsonCodec(array(JsonCodec::PRETTY_PRINT => true));
        $test = array(
            "first" => array(
                "test01" => 0,
                "test02" => 100,
                "test03" => -50,
            ),
            "second" => array(
                1.5,
                -1.5,
                1.25E-16
            ),
            "third" => "hogehoge",
            "fourth" => array(
                "test04" => array(
                    "true" => true,
                    "false" => false,
                    "null" => null,
                ),
            ),
        );
        $result1   = $obj1->encode($test);
        $expected1 = file_get_contents(__DIR__ . "/JsonCodec/encode-default.txt");
        $this->assertSame($expected1, $result1);
        
        $result2   = $obj2->encode($test);
        $expected2 = file_get_contents(__DIR__ . "/JsonCodec/encode-pretty_print.txt");
        $this->assertSame(Strings::getLines($expected2), Strings::getLines($result2));
    }
    
    /**
     * リソース型の encode のテストです.
     * 指定されたリソースを文字列にキャストした結果を encode します.
     * 
     * @covers Peach\DF\JsonCodec::__construct
     * @covers Peach\DF\JsonCodec::encode
     * @covers Peach\DF\JsonCodec::encodeValue
     */
    public function testEncodeResource()
    {
        $codec    = $this->object;
        $resource = fopen(__FILE__, "rb");
        $expected = '"stream #';
        $this->assertStringStartsWith($expected, $codec->encode($resource));
    }
}
