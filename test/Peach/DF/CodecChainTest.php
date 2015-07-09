<?php
namespace Peach\DF;

class CodecChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CodecChain
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $first  = new JsonCodec();
        $second = Base64Codec::getInstance();
        $this->object = new CodecChain($first, $second);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 引数に CodecChain を指定して 3 種類以上の Codec を連結させた場合に,
     * どのような順番で連結させても等価なオブジェクトが生成されることを確認します.
     * 
     * @covers Peach\DF\CodecChain::__construct
     */
    public function test__construct()
    {
        $j1   = new JsonCodec(1);
        $j2   = new JsonCodec(2);
        $j3   = new JsonCodec(3);
        $j4   = new JsonCodec(4);
        $j5   = new JsonCodec(5);
        
        $o1 = new CodecChain($j4, $j5);
        $o2 = new CodecChain($j3, $o1);
        $o3 = new CodecChain($j2, $o2);
        $o4 = new CodecChain($j1, $o3);
        
        $o5 = new CodecChain($j1, $j2);
        $o6 = new CodecChain($o5, $j3);
        $o7 = new CodecChain($o6, $j4);
        $o8 = new CodecChain($o7, $j5);
        
        $this->assertEquals($o4, $o8);
    }
    
    /**
     * 2 番目, 1 番目の順で Codec のデコードが行われることを確認します.
     * 
     * @covers Peach\DF\CodecChain::decode
     */
    public function testDecode()
    {
        $obj      = $this->object;
        $test     = "eyJmb28iOjEsImJhciI6MiwiYmF6IjozfQ==";
        $expected = new \stdClass();
        $expected->foo = 1;
        $expected->bar = 2;
        $expected->baz = 3;
        
        $this->assertEquals($expected, $obj->decode($test));
    }
    
    /**
     * 1 番目, 2 番目の順で Codec のエンコードが行われることを確認します.
     * 
     * @covers Peach\DF\CodecChain::encode
     */
    public function testEncode()
    {
        $obj      = $this->object;
        $test     = array(
            "foo" => 1,
            "bar" => 2,
            "baz" => 3,
        );
        $expected = "eyJmb28iOjEsImJhciI6MiwiYmF6IjozfQ==";
        $this->assertSame($expected, $obj->encode($test));
    }
    
    /**
     * 元のチェーンの末尾に他の Codec を連結した新しい CodecChain
     * が生成されることを確認します.
     * 
     * @covers Peach\DF\CodecChain::append
     */
    public function testAppend()
    {
        $j1   = new JsonCodec(1);
        $j2   = new JsonCodec(2);
        $j3   = new JsonCodec(3);
        
        $test     = new CodecChain($j1, $j2);
        $expected = new CodecChain($j1, new CodecChain($j2, $j3));
        $this->assertEquals($expected, $test->append($j3));
    }
    
    /**
     * 元のチェーンの先頭に他の Codec を連結させた新しい CodecChain
     * が生成されることを確認します.
     * 
     * @covers Peach\DF\CodecChain::prepend
     */
    public function testPrepend()
    {
        $j1   = new JsonCodec(1);
        $j2   = new JsonCodec(2);
        $j3   = new JsonCodec(3);
        
        $test     = new CodecChain($j2, $j3);
        $expected = new CodecChain($j1, $test);
        $this->assertEquals($expected, $test->prepend($j1));
    }
}
