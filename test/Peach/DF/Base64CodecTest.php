<?php
namespace Peach\DF;

class Base64CodecTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Base64Codec
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Base64Codec();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * base64 でエンコードされた文字列をデコードできることを確認します.
     * 
     * @covers Peach\DF\Base64Codec::decode
     */
    public function testDecode()
    {
        $obj      = $this->object;
        $test     = "VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZ3Mu";
        $expected = "The Quick Brown Fox Jumps Over The Lazy Dogs.";
        $this->assertSame($expected, $obj->decode($test));
    }
    
    /**
     * 任意の文字列を base64 でエンコードできることを確認します.
     * 
     * @covers Peach\DF\Base64Codec::encode
     */
    public function testEncode()
    {
        $obj      = $this->object;
        $expected = "VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZ3Mu";
        $test     = "The Quick Brown Fox Jumps Over The Lazy Dogs.";
        $this->assertSame($expected, $obj->encode($test));
    }
}
