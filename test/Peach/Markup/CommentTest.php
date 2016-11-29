<?php
namespace Peach\Markup;
require_once(__DIR__ . "/ContainerTestImpl.php");
require_once(__DIR__ . "/TestContext.php");

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Comment
     */
    protected $object1;
    
    /**
     * @var Comment
     */
    protected $object2;
    
    /**
     * @var Comment
     */
    protected $object3;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object1 = new Comment();
        $this->object2 = new Comment("test-prefix");
        $this->object3 = new Comment("test-prefix", "test-suffix");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * getPrefix() をテストします.
     * 以下を確認します.
     * 
     * - コンストラクタ引数を省略した場合は空文字列となる
     * - コンストラクタの第一引数に指定した文字列を返す
     * 
     * @covers Peach\Markup\Comment::__construct
     * @covers Peach\Markup\Comment::getPrefix
     */
    public function testGetPrefix()
    {
        $obj1 = new Comment();
        $obj2 = new Comment("test-prefix");
        $obj3 = new Comment("test-prefix", "test-suffix");
        $this->assertSame("",            $obj1->getPrefix());
        $this->assertSame("test-prefix", $obj2->getPrefix());
        $this->assertSame("test-prefix", $obj3->getPrefix());
    }

    /**
     * getSuffix() をテストします.
     * 以下を確認します.
     * 
     * - コンストラクタの第二引数を省略した場合は空文字列となる
     * - コンストラクタの第二引数に指定した文字列を返す
     * 
     * @covers Peach\Markup\Comment::getSuffix
     */
    public function testGetSuffix()
    {
        $this->assertSame("",            $this->object1->getSuffix());
        $this->assertSame("",            $this->object2->getSuffix());
        $this->assertSame("test-suffix", $this->object3->getSuffix());
    }

    /**
     * Context の handleComment() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\Comment::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object1->accept($context);
        $this->assertSame("handleComment", $context->getResult());
    }

    /**
     * Container で定義されている appendNode() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\Comment::appendNode
     * @see    Peach\Markup\ContainerTestImpl::testAppend
     */
    public function testAppendNode()
    {
        $test = new ContainerTestImpl($this, $this->object1);
        $test->testAppendNode();
    }
    
    /**
     * このオブジェクト自身を返すことを確認します.
     * 
     * @covers Peach\Markup\Comment::getAppendee
     */
    public function testGetAppendee()
    {
        $obj = $this->object1;
        $this->assertSame($obj, $obj->getAppendee());
    }
    
    /**
     * Container で定義されている getChildNodes() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\Comment::getChildNodes
     * @see    Peach\Markup\ContainerTestImpl::testChildNodes
     */
    public function testGetChildNodes()
    {
        $test = new ContainerTestImpl($this, $this->object1);
        $test->testGetChildNodes();
    }
}
