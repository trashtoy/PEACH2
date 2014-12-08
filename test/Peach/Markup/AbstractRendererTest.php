<?php
namespace Peach\Markup;

abstract class AbstractRendererTest extends \PHPUnit_Framework_TestCase
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
     * @covers Peach\Markup\AbstractRenderer::formatStartTag
     */
    abstract public function testFormatStartTag();
    
    /**
     * @covers Peach\Markup\AbstractRenderer::formatEndTag
     */
    abstract public function testFormatEndTag();
    
    /**
     * @covers Peach\Markup\AbstractRenderer::formatEmptyTag
     */
    abstract public function testFormatEmptyTag();
}
