<?php
namespace Peach\DF;
use Peach\Util\ArrayMap;

class SerializationCodecTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializationCodec
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SerializationCodec();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * serialize された文字列から元の値に復元できることを確認します.
     * 
     * @covers Peach\DF\SerializationCodec::decode
     */
    public function testDecode()
    {
        $obj  = $this->object;
        $map = new ArrayMap();
        $map->put("TEST1", "foo");
        $map->put("TEST2", "bar");
        $map->put("TEST3", "baz");
        
        $test = serialize($map);
        $this->assertEquals($map, $obj->decode($test));
    }
    
    /**
     * encode の結果が, 引数の値を serialize した結果に等しくなることを確認します.
     *  
     * @covers Peach\DF\SerializationCodec::encode
     */
    public function testEncode()
    {
        $obj = $this->object;
        $map = new ArrayMap();
        $map->put("TEST1", "foo");
        $map->put("TEST2", "bar");
        $map->put("TEST3", "baz");
        
        $expected = serialize($map);
        $this->assertSame($expected, $obj->encode($map));
    }
}
