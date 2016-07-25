<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestUtil.php");
use Peach\DT\Datetime;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helper
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Helper(new DefaultBuilder(), array("meta", "input", "br"));
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * createObject() のテストです. 以下について確認します.
     * 
     * - HelperObject 型のオブジェクトを返すこと
     * - 返り値の HelperObject が, createNode() によって生成された Component をラップしていること
     * - 第 2 引数を指定した場合, 生成される HelperObject の要素にその内容が属性としてセットされること
     * - 第 2 引数を指定したが, 生成される HelperObject が要素ではない場合は無視されること
     * 
     * @covers Peach\Markup\Helper::createObject
     */
    public function testCreateObject()
    {
        $h    = $this->object;
        $obj  = $h->createObject("p");
        $this->assertInstanceOf("Peach\\Markup\\HelperObject", $obj);
        
        $h1   = $h->createObject(new ContainerElement("h1"));
        $div  = $h->createObject("div");
        $emp  = $h->createObject("");
        $this->assertEquals(new ContainerElement("h1"),  $h1->getNode());
        $this->assertEquals(new ContainerElement("div"), $div->getNode());
        $this->assertEquals(new NodeList(),              $emp->getNode());
        
        $obj2  = $h->createObject("input", array("type" => "text", "name" => "title", "value" => "sample attribute"));
        $input = new EmptyElement("input");
        $input->setAttributes(array("type" => "text", "name" => "title", "value" => "sample attribute"));
        $this->assertEquals($input, $obj2->getNode());
        
        $obj3  = $h->createObject(null, array("class" => "ignore_test"));
        $this->assertEquals(new NodeList(), $obj3->getNode());
    }
    
    /**
     * createNode() のテストです. 引数によって, 以下の結果が返ることを確認します.
     * 
     * - {@link Node} 型オブジェクトの場合: 引数自身
     * - {@link NodeList} 型オブジェクトの場合: 引数自身
     * - {@link HelperObject} 型オブジェクトの場合: 引数のオブジェクトがラップしているノード
     * - 文字列の場合: 引数の文字列を要素名に持つ新しい {@link Element}
     * - null または空文字列の場合: 空の {@link NodeList}
     * - それ以外: 引数の文字列表現のテキストノード
     * 
     * @covers Peach\Markup\Helper::createNode
     * @covers Peach\Markup\Helper::createElement
     */
    public function testCreateNode()
    {
        $h = $this->object;
        $node = new EmptyElement("br");
        $this->assertSame($node, $h->createNode($node));
        
        $nodeList = new NodeList(array("First", "Second", "Third"));
        $this->assertSame($nodeList, $h->createNode($nodeList));
        
        $div= new ContainerElement("div");
        $div->setAttribute("id", "test");
        $div->appendNode("Sample Text");
        $ho = $h->createObject($div);
        $this->assertSame($div, $h->createNode($ho));
        
        $p = new ContainerElement("p");
        $this->assertEquals($p, $h->createNode("p"));
        
        $emptyList = new NodeList();
        $this->assertEquals($emptyList, $h->createNode(null));
        $this->assertEquals($emptyList, $h->createNode(""));
        
        $datetime = new Datetime(2012, 5, 21, 7, 34);
        $textNode = new Text("2012-05-21 07:34");
        $this->assertEquals($textNode, $h->createNode($datetime));
    }
    
    /**
     * Helper にセットされた Builder の build() の結果が返ることを確認します.
     * @covers Peach\Markup\Helper::write
     */
    public function testWrite()
    {
        $h = $this->object;
        $o = $h->createObject(TestUtil::getTestNode());
        $this->assertSame(TestUtil::getDefaultBuildResult(), $h->write($o));
    }
    
    /**
     * getBuilder() と setBuilder() のテストです. 以下について確認します.
     * 
     * - getBuilder() がコンストラクタの引数に指定した Builder オブジェクトと同一のものを返すこと
     * - setBuilder() で指定した Builder オブジェクトが getBuilder() から取得できること
     * 
     * @covers Peach\Markup\Helper::__construct
     * @covers Peach\Markup\Helper::getBuilder
     * @covers Peach\Markup\Helper::setBuilder
     */
    public function testAccessBuilder()
    {
        $b1 = new DefaultBuilder();
        $b1->setIndent(new Indent(0, Indent::TAB, Indent::LF));
        $b2 = new DefaultBuilder();
        $b2->setBreakControl(MinimalBreakControl::getInstance());
        
        $h = new Helper($b1);
        $this->assertSame($b1, $h->getBuilder());
        $h->setBuilder($b2);
        $this->assertSame($b2, $h->getBuilder());
    }
}
