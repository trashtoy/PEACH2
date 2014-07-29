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
        $obj->append("TEXT");
        $obj->append(new EmptyElement("test"));
        $obj->append(None::getInstance()); // added none
        $obj->append($nList); // added 3 nodes
        $test->assertSame(5, count($obj->getChildNodes()));
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
