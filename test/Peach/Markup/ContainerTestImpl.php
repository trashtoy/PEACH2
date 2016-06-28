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
     * append() をテストします.
     * 以下を確認します.
     * 
     * - 任意のスカラー値および Node が追加できること
     * - null および None を指定した場合, 変化がない (追加されない) こと
     * - コンテナを追加した場合, 自身ではなくその子ノードが追加されること
     * 
     * @covers Peach\Markup\Container::append
     */
    public function testAppend()
    {
        $nList = new NodeList();
        $nList->append("foo");
        $nList->append("bar");
        $nList->append("baz");
        
        $test  = $this->test;
        $obj   = $this->object;
        $obj->append(null);
        $obj->append("TEXT");                           // (count: 1)
        $obj->append(new EmptyElement("test"));         // (count: 2)
        $obj->append(None::getInstance());              // added nothing (count: 2)
        $obj->append($nList);                           // added 3 nodes (count: 5)
        $obj->append(array("A", "B", array("C", "D"))); // added 4 nodes (count: 9)
        $test->assertSame(9, count($obj->getChildNodes()));
        
        $this->testAppendHelperObject();
    }
    
    /**
     * HelperObject を引数として append() を実行した場合,
     * HelperObject が持つ Component オブジェクトに対して
     * append() の処理が適用されることを確認します.
     * 
     * @covers Peach\Markup\Container::append
     */
    private function testAppendHelperObject()
    {
        $test   = $this->test;
        $obj    = $this->object;
        $helper = new Helper(new DefaultBuilder());
        $node1  = $helper->createObject("div")->append("Test 1")->append("Test 2")->append("Test 3");
        $obj->append($node1);
        
        $expected = new ContainerElement("div");
        $expected->append("Test 1");
        $expected->append("Test 2");
        $expected->append("Test 3");

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
        $obj->append("SAMPLE TEXT 1");
        $obj->append(new Code("SAMPLE CODE 2"));
        $obj->append(new Text("SAMPLE TEXT 3"));
        
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
        $obj->append("TEST");
        $obj->append(new EmptyElement("sample"));
        $obj->append(array("first", "second", "third"));
        $test->assertSame(5, $obj->size());
    }
}
