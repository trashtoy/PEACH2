<?php

namespace Peach\Http;

use Peach\DF\JsonCodec;
use Peach\Http\Body;
use Peach\Http\Body\CodecRenderer;
use Peach\Http\Body\StringRenderer;
use PHPUnit_Framework_TestCase;

class BodyTest extends PHPUnit_Framework_TestCase
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
     * コンストラクタ引数に指定した値と同じものを返すことを確認します.
     * 
     * @covers Peach\Http\Body::getValue
     * @covers Peach\Http\Body::__construct
     */
    public function testGetValue()
    {
        $obj = new Body("Hello World", StringRenderer::getInstance());
        $this->assertSame("Hello World", $obj->getValue());
    }
    
    /**
     * コンストラクタ引数に指定した値と同じものを返すことを確認します.
     * 
     * @covers Peach\Http\Body::getRenderer
     * @covers Peach\Http\Body::__construct
     */
    public function testGetRenderer()
    {
        $renderer = new CodecRenderer(new JsonCodec());
        $obj      = new Body(array("foo", "bar", "baz"), $renderer);
        $this->assertSame($renderer, $obj->getRenderer());
    }
}
