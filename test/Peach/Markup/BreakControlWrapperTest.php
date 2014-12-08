<?php
namespace Peach\Markup;

class BreakControlWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BreakControlWrapper
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new BreakControlWrapper();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの引数にしたものと同じオブジェクトを返すことを確認します.
     * @covers Peach\Markup\BreakControlWrapper::getOriginal
     */
    public function testGetOriginal()
    {
        $original = DefaultBreakControl::getInstance();
        $wrapper  = new BreakControlWrapper($original);
        $this->assertSame($original, $wrapper->getOriginal());
    }
    
    /**
     * ラップ対象のオブジェクトと同じ結果を返すことを確認します.
     * @covers Peach\Markup\BreakControlWrapper::breaks
     */
    public function testBreaks()
    {
        $obj = $this->object;
        $e5  = new ContainerElement("span");
        $e5->append("some text");
        $this->assertFalse($obj->breaks($e5));
        
        $e6  = new ContainerElement("span");
        $e6->append("first text");
        $e6->append("second test");
        $this->assertTrue($obj->breaks($e6));
    }
}
