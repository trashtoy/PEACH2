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
        $this->object = Base64Codec::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * getInstance() のテストです. 以下を確認します.
     * 
     * - Base64Codec クラスのインスタンスを返すことを確認します.
     * - 常に同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\DF\Base64Codec::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = Base64Codec::getInstance();
        $obj2 = Base64Codec::getInstance();
        $this->assertSame("Peach\\DF\\Base64Codec", get_class($obj1));
        $this->assertSame($obj1, $obj2);
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
