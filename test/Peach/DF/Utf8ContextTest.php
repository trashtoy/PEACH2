<?php
namespace Peach\DF;

class Utf8ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 文字列 "Test" です.
     * @var string
     */
    private static $test;
    
    /**
     * 文字列 "テスト" (マルチバイト) です.
     * @var string
     */
    private static $testMB;
    
    public static function setUpBeforeClass()
    {
        self::$test   = "Test";
        self::$testMB = implode("", array_map("chr", array(0xE3, 0x83, 0x86, 0xE3, 0x82, 0xB9, 0xE3, 0x83, 0x88))); // "テスト"
    }
    
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
     * @covers Peach\DF\Utf8Context::next
     * @covers Peach\DF\Utf8Context::getCharCount
     * @covers Peach\DF\Utf8Context::nextMultibyteChar
     * @covers Peach\DF\Utf8Context::getUnicodeNumber
     * @covers Peach\DF\Utf8Context::__construct
     */
    public function testNext()
    {
        $test   = self::$test;
        $testMB = self::$testMB;
        $c1 = new Utf8Context($test);
        $this->assertSame(0x54, $c1->next());
        $this->assertSame(0x65, $c1->next());
        $this->assertSame(0x73, $c1->next());
        $this->assertSame(0x74, $c1->next());
        $this->assertNull($c1->next());
        
        $c2 = new Utf8Context($testMB);
        $this->assertSame(0x30c6, $c2->next()); // Unicode "テ"
        $this->assertSame(0x30b9, $c2->next()); // Unicode "ス"
        $this->assertSame(0x30c8, $c2->next()); // Unicode "ト"
        $this->assertNull($c2->next());
        
        $ng1 = "T" . chr(0x90) . "e" . chr(0xA0) . "s" . chr(0xB0) . "t";
        $c3 = new Utf8Context($ng1);
        $this->assertSame(0x54, $c3->next());
        $this->assertSame(0x65, $c3->next());
        $this->assertSame(0x73, $c3->next());
        $this->assertSame(0x74, $c3->next());
        $this->assertNull($c3->next());
        
        // E3 のみを入力した場合
        // 3 バイト文字のはずが 1 バイトしかないため, 足りない分を 0x80 で補完する.
        // その結果 E38080 として扱われるため, 結果は 0x3000 (全角スペース) となる.
        $ng2 = chr(0xE3);
        $c4  = new Utf8Context($ng2);
        $this->assertSame(0x3000, $c4->next());
        $this->assertNull($c4->next());
        
        $ng3 = "T" . chr(0xE3) . "e" . chr(0xE3) . chr(0x80) . "s" . chr(0xC2) . "t";
        $c5  = new Utf8Context($ng3);
        $this->assertSame(0x54, $c5->next());
        $this->assertSame(0x65, $c5->next());
        $this->assertSame(0x73, $c5->next());
        $this->assertSame(0x74, $c5->next());
        $this->assertNull($c5->next());
    }
    
    /**
     * hasNext() のテストです. 以下を確認します.
     * 
     * - デフォルトは true を返すこと
     * - next() を文字数だけ実行した後は false を返すこと
     * 
     * @covers Peach\DF\Utf8Context::hasNext
     * @covers Peach\DF\Utf8Context::__construct
     */
    public function testHasNext()
    {
        $context = new Utf8Context(self::$testMB);
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($context->hasNext());
            $context->next();
        }
        $this->assertFalse($context->hasNext());
    }
}
