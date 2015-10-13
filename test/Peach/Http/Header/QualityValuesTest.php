<?php
namespace Peach\Http\Header;

class QualityValuesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QualityValues
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QualityValues("Accept-Language", array("ja" => 1.0, "en-US" => 0.9, "en-GB" => 0.8, "en" => 0.7));
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * @covers Peach\Http\Header\QualityValues::format
     */
    public function testFormat()
    {
        $obj = $this->object;
        $this->assertSame("ja,en-US;q=0.9,en-GB;q=0.8,en;q=0.7", $obj->format());
    }
    
    /**
     * @covers Peach\Http\Header\QualityValues::getName
     */
    public function testGetName()
    {
        $obj = $this->object;
        $this->assertSame("Accept-Language", $obj->getName());
    }
}
