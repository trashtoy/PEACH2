<?php
namespace Peach\Http\Body;

use Peach\DF\Base64Codec;
use PHPUnit_Framework_TestCase;

class CodecRendererTest extends PHPUnit_Framework_TestCase
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
     * @covers Peach\Http\Body\CodecRenderer::__construct
     * @covers Peach\Http\Body\CodecRenderer::render
     */
    public function testRender()
    {
        $codec = Base64Codec::getInstance();
        $obj   = new CodecRenderer($codec);
        $this->assertSame("SGVsbG8gV29ybGQ=", $obj->render("Hello World"));
    }
}
