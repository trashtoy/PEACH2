<?php
namespace Peach\DT;

abstract class AbstractTimeTest extends \PHPUnit_Framework_TestCase
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
     * @covers Peach\DT\AbstractTime::get
     */
    public abstract function testGet();
    
    /**
     * @covers Peach\DT\AbstractTime::set
     */
    public abstract function testSet();
    
    /**
     * @covers Peach\DT\AbstractTime::setAll
     */
    public abstract function testSetAll();
    
    /**
     * @covers Peach\DT\AbstractTime::add
     */
    public abstract function testAdd();
    
    /**
     * @covers Peach\DT\AbstractTime::compareTo
     */
    public abstract function testCompareTo();
    
    /**
     * @covers Peach\DT\AbstractTime::format
     */
    public abstract function testFormat();
    
    /**
     * @covers Peach\DT\AbstractTime::equals
     */
    public abstract function testEquals();
    
    /**
     * @covers Peach\DT\AbstractTime::before
     */
    public abstract function testBefore();
    
    /**
     * @covers Peach\DT\AbstractTime::after
     */
    public abstract function testAfter();
    
    /**
     * @covers Peach\DT\AbstractTime::formatTime
     */
    public abstract function testFormatTime();
    
    /**
     * @covers Peach\DT\AbstractTime::__toString
     */
    public abstract function test__toString();
}
