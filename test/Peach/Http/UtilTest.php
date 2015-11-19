<?php
namespace Peach\Http;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class UtilTest extends PHPUnit_Framework_TestCase
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
     * validateHeaderName() のテストです.
     * 
     * 以下の文字列が妥当と判定されることを確認します.
     * 
     * - 半角アルファベット・数字・ハイフンから成る文字列
     * - 擬似ヘッダー ":authority", ":path", ":method", ":scheme", ":status"
     * 
     * @covers Peach\Http\Util::validateHeaderName
     */
    public function testValidateHeaderName()
    {
        $validList = array(
            "X-Powered-By",
            ":authority",
            ":path",
            ":method",
            ":scheme",
            ":status",
        );
        foreach ($validList as $name) {
            Util::validateHeaderName($name);
        }
    }
    
    /**
     * ヘッダー名として不正な文字列を指定した場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * - アルファベット・数字・ハイフン以外の ASCII 文字 (空白や記号など)
     * - マルチバイト文字
     * - 定義されていない擬似ヘッダー
     * - 大文字を含む擬似ヘッダー
     * 
     * @covers Peach\Http\Util::validateHeaderName
     */
    public function testValidateHeaderNameFail()
    {
        $errorList = array(
            "Last-Modified\r\n",
            "  Accept  ",
            "テスト",
            ":hoge", // 不明な擬似ヘッダーは NG とする
            ":SCHEME", // 擬似ヘッダーは大文字・小文字を区別する
        );
        foreach ($errorList as $name) {
            try {
                Util::validateHeaderName($name);
            } catch (InvalidArgumentException $e) {
                continue;
            }
            
            $this->fail("'{$name}' must be treated as invalid");
        }
    }
    
    /**
     * validateHeaderValue() のテストです.
     * 
     * 先頭および末尾が印字可能な US-ASCII 文字であり,
     * その中間が任意の個数の印字可能 US-ASCII もしくは空白文字で構成されれている場合に妥当とします.
     * RFC 7230 の定義により空文字列も妥当と判定します.
     * 
     * @covers Peach\Http\Util::validateHeaderValue
     * @covers Peach\Http\Util::handleValidateHeaderValue
     * @covers Peach\Http\Util::validateBytes
     * @covers Peach\Http\Util::validateVCHAR
     */
    public function testValidateHeaderValue()
    {
        $validList = array(
            "var",
            "",
            "The Quick Brown Fox Jumps Over The Lazy Dogs",
            "=?iso-8859-1?q?this=20is=20some=20text?=", // RFC 2047
        );
        foreach ($validList as $value) {
            Util::validateHeaderValue($value);
        }
    }
    
    /**
     * ヘッダー値として不正な文字列を指定した場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * - VCHAR (%x21-%x7E) の範囲外の文字
     * - 文字列の先頭・末尾にあるホワイトスペース
     * - obs-fold (RFC7230 により廃止された複数行によるヘッダー値)
     */
    public function testValidateHeaderValueFail()
    {
        $errorList = array(
            "This is テスト",
            "  foobar  ",
            "abc\r\n  xyz",
        );
        foreach ($errorList as $value) {
            try {
                Util::validateHeaderValue($value);
            } catch (InvalidArgumentException $e) {
                continue;
            }
            
            $this->fail("'{$value}' must be treated as invalid");
        }
    }
    
    /**
     * @covers Peach\Http\Util::parseHeader
     * @covers Peach\Http\Util::parseQualityValue
     * @covers Peach\Http\Util::parseQvalue
     */
    public function testParseHeader()
    {
        $q1 = array(
            "text/html"             => 1.0,
            "application/xhtml+xml" => 1.0,
            "application/xml"       => 0.9,
            "image/webp"            => 1.0,
            "*/*"                   => 0.8,
        );
        $h1 = new Header\QualityValues("accept", $q1);
        $this->assertEquals($h1, Util::parseHeader("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"));
        $this->assertEquals($h1, Util::parseHeader("Accept", "text/html;x,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"));
        
        $r1 = new Header\Raw("connection", "keep-alive");
        $this->assertEquals($r1, Util::parseHeader("Connection", "keep-alive"));
    }
}
