<?php
namespace Peach\Markup;

abstract class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $object;
    
    /**
     * @var Component
     */
    protected $testNode;
    
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
     * @covers Peach\Markup\Context::handle
     * @covers Peach\Markup\Context::getResult
     */
    public abstract function testGetResult();
}
