<?php
namespace Peach\DF\JsonCodec;

use Peach\Util\ArrayMap;
use Peach\DF\JsonCodec;

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
     * @covers Peach\DF\JsonCodec\Context::getOption
     */
    public function testGetOption()
    {
        $options = new ArrayMap();
        $options->put(JsonCodec::BIGINT_AS_STRING, true);
        $context = new Context("This is a pen.", $options);
        $this->assertFalse($context->getOption(JsonCodec::OBJECT_AS_ARRAY));
        $this->assertTrue($context->getOption(JsonCodec::BIGINT_AS_STRING));
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::__construct
     * @covers Peach\DF\JsonCodec\Context::hasNext
     */
    public function testHasNext()
    {
        $context = new Context("This is a pen.", new ArrayMap());
        
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
        $context  = new Context("", new ArrayMap());
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
        $context = new Context("This is a pen.", new ArrayMap());
        
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
     * @covers Peach\DF\JsonCodec\Context::currentCodePoint
     */
    public function testCurrentCodePoint()
    {
        $context = new Context("Test", new ArrayMap());
        $this->assertSame(0x54, $context->currentCodePoint());
        $context->next();
        $this->assertSame(0x65, $context->currentCodePoint());
        $context->next();
        $this->assertSame(0x73, $context->currentCodePoint());
        $context->next();
        $this->assertSame(0x74, $context->currentCodePoint());
        $context->next();
        $this->assertNull($context->currentCodePoint());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::current
     * @covers Peach\DF\JsonCodec\Context::computeCurrent
     * @covers Peach\DF\JsonCodec\Context::next
     */
    public function testCurrentWithBreakcode()
    {
        $context = new Context("\n\n\r\r\r\n\r\n", new ArrayMap());
        $this->assertSame("\n", $context->current());
        $this->assertSame("\n", $context->next());
        $this->assertSame("\r", $context->next());
        $this->assertSame("\r", $context->next());
        $this->assertSame("\r\n", $context->next());
        $this->assertSame("\r\n", $context->next());
    }
    
    /**
     * "エラー文言 at line 行数, column 列数" 形式のエラーメッセージを持つ
     * DecodeException を生成することを確認します.
     * 
     * @covers Peach\DF\JsonCodec\Context::createException
     */
    public function testCreateException()
    {
        $context = new Context("This\nis\r\na pen.", new ArrayMap());
        
        // read "This is a "
        for ($i = 0; $i < 10; $i++) {
            $context->next();
        }
        $e = $context->createException("Test");
        $this->assertSame("Test at line 3, column 3", $e->getMessage());
    }
    
    /**
     * @covers Peach\DF\JsonCodec\Context::next
     * @expectedException Peach\DF\JsonCodec\DecodeException
     */
    public function testNextFail()
    {
        $context = new Context("This is a pen.", new ArrayMap());
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
        $context = new Context("This is a pen.", new ArrayMap());
        
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
        $context = new Context("This is a pen.", new ArrayMap());
        
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
        $context = new Context("This is a pen.", new ArrayMap());
        
        // read "This is a "
        $context->skip(10);
        
        // The remaining count is 4, but skipping 5
        $context->skip(5);
    }
}
