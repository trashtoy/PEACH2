<?php
namespace Peach\DF\JsonCodec;

class ContextTest extends \PHPUnit_Framework_TestCase
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
     * @covers Peach\DF\JsonCodec\Context::__construct
     * @covers Peach\DF\JsonCodec\Context::hasNext
     */
    public function testHasNext()
    {
        $context = new Context("This is a pen.");
        
        // read "This "
        for ($i = 0; $i < 5; $i++) {
            $context->next();
        }
        $this->assertTrue($context->hasNext());
        
        // read "is a pen."
        for ($i = 0; $i < 9; $i++) {
            $context->next();
        }
        $this->assertFalse($context->hasNext());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::encodeCodepoint
     */
    public function testEncodeCodepoint()
    {
        $context  = new Context("");
        $chr      = chr(227) . chr(129) . chr(130); // "あ";
        $this->assertSame($chr, $context->encodeCodepoint(0x3042));
    }

    /**
     * @covers Peach\DF\JsonCodec\Context::__construct
     * @covers Peach\DF\JsonCodec\Context::current
     * @covers Peach\DF\JsonCodec\Context::computeCurrent
     * @covers Peach\DF\JsonCodec\Context::next
     */
    public function testCurrentAndNext()
    {
        $context = new Context("This is a pen.");
        
        // read "This "
        for ($i = 0; $i < 5; $i++) {
            $context->next();
        }
        $this->assertSame("i", $context->current());
        
        // read "is a pen."
        for ($i = 0; $i < 9; $i++) {
            $context->next();
        }
        $this->assertNull($context->current());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::current
     * @covers Peach\DF\JsonCodec\Context::computeCurrent
     * @covers Peach\DF\JsonCodec\Context::next
     */
    public function testCurrentWithBreakcode()
    {
        $context = new Context("\n\n\r\r\r\n\r\n");
        $this->assertSame("\n", $context->current());
        $this->assertSame("\n", $context->next());
        $this->assertSame("\r", $context->next());
        $this->assertSame("\r", $context->next());
        $this->assertSame("\r\n", $context->next());
        $this->assertSame("\r\n", $context->next());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::throwException
     */
    public function testThrowException()
    {
        $context = new Context("This\nis\r\na pen.");
        
        // read "This is a "
        for ($i = 0; $i < 10; $i++) {
            $context->next();
        }
        try {
            $context->throwException("Test");
            $this->fail();
        } catch (DecodeException $e) {
            $this->assertSame("Test at line 3, column 3", $e->getMessage());
        }
    }

    /**
     * @covers Peach\DF\JsonCodec\Context::next
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testNextFail()
    {
        $context = new Context("This is a pen.");
        for ($i = 0; $i < 14; $i++) {
            $context->next();
        }
        $this->assertFalse($context->hasNext());
        $context->next();
    }

    /**
     * @covers Peach\DF\JsonCodec\Context::getSequence
     * @covers Peach\DF\JsonCodec\Context::encodeIndex
     */
    public function testGetSequence()
    {
        $context = new Context("This is a pen.");
        
        // read "this "
        $context->skip(5);
        $this->assertSame("is a", $context->getSequence(4));
        
        // read "is a "
        $context->skip(5);
        $this->assertSame("pen.", $context->getSequence(10));
    }

    /**
     * @covers Peach\DF\JsonCodec\Context::skip
     */
    public function testSkip()
    {
        $context = new Context("This is a pen.");
        
        // read "This "
        $context->skip(5);
        $this->assertSame("i", $context->current());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::skip
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testSkipFail()
    {
        $context = new Context("This is a pen.");
        
        // read "This is a "
        $context->skip(10);
        
        // The remaining count is 4, but skipping 5
        $context->skip(5);
    }
}
