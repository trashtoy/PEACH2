<?php
namespace Peach\Markup;

class ContainerTestImpl
{
    /**
     * @var Container
     */
    private $object;
    
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    protected $test;
    
    public function __construct(\PHPUnit_Framework_TestCase $test, Container $object)
    {
        $this->test   = $test;
        $this->object = $object;
    }
    
    /**
     * appendNode() をテストします.
     * 以下を確認します.
     * 
     * - 任意のスカラー値および Node が追加できること
     * - null および None を指定した場合, 変化がない (追加されない) こと
     * - コンテナを追加した場合, 自身ではなくその子ノードが追加されること
     * 
     * @covers Peach\Markup\Container::appendNode
     */
    public function testAppendNode()
    {
        $nList = new NodeList();
        $nList->appendNode("foo");
        $nList->appendNode("bar");
        $nList->appendNode("baz");
        
        $test  = $this->test;
        $obj   = $this->object;
        $obj->appendNode(null);
        $obj->appendNode("TEXT");                           // (count: 1)
        $obj->appendNode(new EmptyElement("test"));         // (count: 2)
        $obj->appendNode(None::getInstance());              // added nothing (count: 2)
        $obj->appendNode($nList);                           // added 3 nodes (count: 5)
        $obj->appendNode(array("A", "B", array("C", "D"))); // added 4 nodes (count: 9)
        $test->assertSame(9, count($obj->getChildNodes()));
        
        $this->testAppendHelperObject();
    }
    
    /**
     * HelperObject を引数として appendNode() を実行した場合,
     * HelperObject が持つ Component オブジェクトに対して
     * appendNode() の処理が適用されることを確認します.
     * 
     * @covers Peach\Markup\Container::append
     */
    private function testAppendHelperObject()
    {
        $test   = $this->test;
        $obj    = $this->object;
        $helper = new BaseHelper(new DefaultBuilder());
        $node1  = $helper->tag("div")->append("Test 1")->append("Test 2")->append("Test 3");
        $obj->appendNode($node1);
        
        $expected = new ContainerElement("div");
        $expected->appendNode("Test 1");
        $expected->appendNode("Test 2");
        $expected->appendNode("Test 3");

        $childNodes = $obj->getChildNodes();
        $test->assertSame(10, count($childNodes)); // append 1 node (count: 10)
        $test->assertEquals($expected, $childNodes[9]);
    }
    
    /**
     * getChildNodes() をテストします.
     * 以下を確認します.
     * 
     * - 初期状態では空の配列を返すこと
     * - Comment ノードに append した一覧を返すこと
     * 
     * @covers Container::getChildNodes
     */
    public function testGetChildNodes()
    {
        $test  = $this->test;
        $obj   = $this->object;
        $test->assertSame(array(), $obj->getChildNodes());
        $obj->appendNode("SAMPLE TEXT 1");
        $obj->appendNode(new Code("SAMPLE CODE 2"));
        $obj->appendNode(new Text("SAMPLE TEXT 3"));
        
        $children = $obj->getChildNodes();
        $test->assertSame(3, count($children));
        $test->assertInstanceOf("Peach\\Markup\\Code", $children[1]);
    }
    
    /**
     * 追加されたノードの個数を返すことを確認します.
     * 
     * @covers Peach\Markup\ContainerElement::size
     * @covers Peach\Markup\NodeList::size
     */
    public function testSize()
    {
        $obj   = $this->object;
        $test  = $this->test;
        $test->assertSame(0, $obj->size());
        $obj->appendNode("TEST");
        $obj->appendNode(new EmptyElement("sample"));
        $obj->appendNode(array("first", "second", "third"));
        $test->assertSame(5, $obj->size());
    }
}
