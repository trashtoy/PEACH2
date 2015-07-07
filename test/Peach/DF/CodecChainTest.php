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
}
