<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestContext.php");
require_once(__DIR__ . "/ContainerTestImpl.php");

class NodeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeList
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new NodeList();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタのテストです. 以下を確認します.
     * 
     * - 引数なしで初期化した場合, 子ノードを持たないインスタンスが生成されること
     * - 引数を指定して初期化した場合, 引数のノードを子ノードとして持つインスタンスが生成されること
     * 
     * @covers Peach\Markup\NodeList::__construct
     */
    public function test__construct()
    {
        $l1 = new NodeList();
        $this->assertSame(array(), $l1->getChildNodes());
        
        $l2         = new NodeList(array("foo", "bar", "baz"));
        $childNodes = $l2->getChildNodes();
        $this->assertEquals(new Text("baz"), $childNodes[2]);
    }
    
    /**
     * Container で定義されている appendNode() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\NodeList::appendNode
     * @covers Peach\Markup\NodeList::getAppendee
     * @see    Peach\Markup\ContainerTestImpl::testAppend
     */
    public function testAppendNode()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testAppendNode();
    }
    
    /**
     * 自分自身を含むノードを引数に appendNode() を実行した場合に例外をスローすることを確認します.
     * 
     * @covers Peach\Markup\NodeList::append
     * @covers Peach\Markup\NodeList::checkOwner
     * @expectedException \InvalidArgumentException
     */
    public function testAppendNodeFail()
    {
        $a        = new ContainerElement("a");
        $b        = new ContainerElement("b");
        $c        = new ContainerElement("c");
        $nodeList = new NodeList(array($a, $b, $c));
        $c->appendNode($nodeList);
    }
    
    /**
     * Context の handleNodeList() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\NodeList::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleNodeList", $context->getResult());
    }
    
    /**
     * 追加されたノードの個数を返すことを確認します.
     * 
     * @covers Peach\Markup\NodeList::size
     */
    public function testSize()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testSize();
    }
    
    /**
     * Container で定義されている getChildNodes() の仕様通りに動作することを確認します.
     * 
     * @covers Peach\Markup\NodeList::getChildNodes
     * @see    Peach\Markup\ContainerTestImpl::testChildNodes
     */
    public function testGetChildNodes()
    {
        $test = new ContainerTestImpl($this, $this->object);
        $test->testGetChildNodes();
    }
}
