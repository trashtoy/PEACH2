<?php
namespace Peach\DT;

class DefaultClockTest extends \PHPUnit_Framework_TestCase
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
     * getInstance() のテストです. 以下を確認します.
     * 
     * - DefaultClock オブジェクトを返す
     * - 複数回実行した際に同一のオブジェクトを返す
     * 
     * @covers Peach\DT\DefaultClock::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = DefaultClock::getInstance();
        $obj2 = DefaultClock::getInstance();
        $this->assertInstanceOf("Peach\DT\DefaultClock", $obj1);
        $this->assertSame($obj1, $obj2);
    }
    
    /**
     * システム時刻と同じ unix time をあらわす Timestamp オブジェクトを返すことを確認します.
     *
     * @covers Peach\DT\DefaultClock::getUnixTime
     * @covers Peach\DT\Clock::getTimestamp
     */
    public function testGetTimestamp()
    {
        $obj    = DefaultClock::getInstance();
        $now    = UnixTimeFormat::getInstance()->parseTimestamp(time());
        $result = $obj->getTimestamp();
        $this->assertEquals($now, $result);
    }
}
