<?php
namespace Peach\Util;

class HashMapEntryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HashMapEntry
     */
    protected $object;
    
    /**
     *
     * @var HashMap
     */
    private $map;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->map = new HashMap();
        $this->map->put("First", 1);
        $entryList = $this->map->entryList();
        $this->object = $entryList[0];
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 引数の Equator で等価と判断される場合のみ true を返すことを確認します.
     * 
     * @covers Peach\Util\HashMapEntry::keyEquals
     */
    public function testKeyEquals()
    {
        $entry = $this->object;
        $e     = DefaultEquator::getInstance();
        $this->assertFalse($entry->keyEquals("first", $e));
        $this->assertTrue($entry->keyEquals("First", $e));
    }
    
    /**
     * HashMapEntry に対する操作が, HashMap に適用されていることを確認します.
     * 
     * @covers Peach\Util\HashMapEntry::setValue
     */
    public function testSetValue()
    {
        $entry = $this->object;
        $entry->setValue(100);
        $this->assertSame(100, $this->map->get("First"));
    }
}
