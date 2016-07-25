<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestUtil.php");
use Peach\DT\Datetime;

class HelperObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helper
     */
    protected $helper;
    
    /**
     * @var HelperObject
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->helper = new Helper(new DefaultBuilder(), array("meta", "input", "br"));
        $this->object = new HelperObject($this->helper, "sample");
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの第 2 引数に指定された値によって, 返される値が変化することを確認します.
     * 
     * - {@link Node Node} 型オブジェクトの場合: 同一のオブジェクト
     * - {@link HelperObject HelperObject} 型オブジェクトの場合: 引数のオブジェクトがラップしているノード
     * - 文字列の場合: 引数の文字列を要素名に持つ新しい {@link Element}
     * - null または空文字列の場合: 空の {@link NodeList}
     * - それ以外: 引数の文字列表現のテキストノード
     * 
     * @covers Peach\Markup\HelperObject::__construct
     * @covers Peach\Markup\HelperObject::getNode
     */
    public function testGetNode()
    {
        $h    = $this->helper;
        
        $node = new EmptyElement("br");
        $obj1 = new HelperObject($h, $node);
        $this->assertSame($node, $obj1->getNode());
        
        $div  = new ContainerElement("div");
        $div->setAttribute("id", "test");
        $div->appendNode("Sample Text");
        $ho   = new HelperObject($h, $div);
        $obj2 = new HelperObject($h, $ho);
        $this->assertSame($div, $obj2->getNode());
        
        $obj3 = new HelperObject($h, "p");
        $this->assertEquals(new ContainerElement("p"), $obj3->getNode());
        
        $emptyList = new NodeList();
        $obj4      = new HelperObject($h, null);
        $this->assertEquals($emptyList, $obj4->getNode());
        $obj5      = new HelperObject($h, "");
        $this->assertEquals($emptyList, $obj5->getNode());
        
        $datetime = new Datetime(2012, 5, 21, 7, 34);
        $textNode = new Text("2012-05-21 07:34");
        $obj6     = new HelperObject($h, $datetime);
        $this->assertEquals($textNode, $obj6->getNode());
    }
    
    /**
     * ラップしているノードが Container の場合は子ノードが追加され,
     * そうでない場合は何も変化しないことを確認します.
     * 
     * @covers Peach\Markup\HelperObject::appendNode
     */
    public function testAppendNode()
    {
        $h    = $this->helper;
        
        $obj1 = new HelperObject($h, "p");
        $obj1->appendNode("Sample Text");
        $p    = new ContainerElement("p");
        $p->appendNode("Sample Text");
        $this->assertEquals($p,  $obj1->getNode());
        
        $obj2 = new HelperObject($h, "br");
        $obj2->appendNode("Sample Text");
        $br   = new EmptyElement("br");
        $this->assertEquals($br, $obj2->getNode());
    }
    
    /**
     * 引数の Container の中に自分自身が追加されることを確認します.
     * 
     * @covers Peach\Markup\HelperObject::appendTo
     */
    public function testAppendTo()
    {
        $h = $this->helper;
        
        $div = new ContainerElement("div");
        $obj = new HelperObject($h, "p");
        $obj->append("Sample Text")->appendTo($div);
        
        $childNodes = $div->getChildNodes();
        $this->assertNotEmpty($childNodes);
        
        $p = new ContainerElement("p");
        $p->appendNode("Sample Text");
        $this->assertEquals($p, $childNodes[0]);
    }
    
    /**
     * appendCode() のテストです. 以下を確認します.
     * 
     * - 引数に {@link Code} オブジェクトを指定した場合, 引数をそのまま子ノードとして追加すること
     * - 引数にそれ以外の値を指定した場合, 引数を Code オブジェクトに変換して子ノードとして追加すること
     * 
     * @covers Peach\Markup\HelperObject::appendCode
     */
    public function testAppendCode()
    {
        $h      = $this->helper;
        $str    = implode("\n", array(
            "$(function() {",
            "    $('#menu').hide();",
            "}",
        ));
        $code   = new Code($str);
        $script = new ContainerElement("script");
        $script->appendNode($code);
        
        $obj1   = new HelperObject($h, "script");
        $obj1->appendCode($code);
        $this->assertEquals($script, $obj1->getNode());
        
        $obj2   = new HelperObject($h, "script");
        $obj2->appendCode($str);
        $this->assertEquals($script, $obj2->getNode());
    }
    
    /**
     * attr() のテストです. 以下を確認します.
     * 
     * - このオブジェクトがラップしているノードが Element ではなかった場合, 何も変化しないこと
     * - 引数が空の場合, 何も変化しないこと
     * - 引数に文字列を 1 つ指定した場合, 指定された名前の boolean 属性が追加されること
     * - 引数に文字列を 2 つ指定した場合, 指定された属性名および属性値を持つ属性が追加されること
     * - 引数に配列を指定した場合, キーを属性名, 値を属性値とする属性が追加されること
     * 
     * @covers Peach\Markup\HelperObject::attr
     */
    public function testAttr()
    {
        $h    = $this->helper;
        $obj1 = new HelperObject($h, "");
        $obj1->attr(array("class" => "test"));
        $this->assertEquals(new NodeList(), $obj1->getNode());
        
        $obj2 = new HelperObject($h, "input");
        $obj2->attr();
        $this->assertEquals(array(), $obj2->getNode()->getAttributes());
        
        $obj2->attr("readonly");
        $obj2->attr("class", "test");
        $obj2->attr(array("name" => "age", "value" => 18));
        $attrList = array(
            "readonly" => null,
            "class"    => "test",
            "name"     => "age",
            "value"    => "18",
        );
        $this->assertEquals($attrList, $obj2->getNode()->getAttributes());
    }
    
    /**
     * children() のテストです. 以下の結果が返ることを確認します.
     * 
     * - ラップしているオブジェクトが NodeList だった場合は, そのオブジェクト自身
     * - ラップしているオブジェクトが Container だった場合は, その子ノード一覧をあらわす HelperObject
     * - それ以外は空の NodeList を表現する HelperObject
     * 
     * @covers Peach\Markup\HelperObject::children
     */
    public function testChildren()
    {
        $h = $this->helper;
        
        $obj1      = new HelperObject($h, null);
        $obj1->append("First")->append("Second")->append("Third");
        $this->assertSame($obj1, $obj1->children());
        
        $p         = new ContainerElement("p");
        $p->appendNode("First");
        $p->appendNode("Second");
        $p->appendNode("Third");
        $obj2      = new HelperObject($h, $p);
        $this->assertEquals($obj1, $obj2->children());
        
        $expected  = new HelperObject($h, null);
        $br        = new EmptyElement("br");
        $obj3      = new HelperObject($h, $br);
        $this->assertEquals($expected, $obj3->children());
    }
    
    /**
     * このオブジェクトに紐付いている Builder の build() が実行されることを確認します.
     * @covers Peach\Markup\HelperObject::write
     */
    public function testWrite()
    {
        $h1   = $this->helper;
        $obj1 = TestUtil::createTestHelperObject($h1);
        $this->assertSame(TestUtil::getDefaultBuildResult(), $obj1->write());
        
        $b    = new DefaultBuilder();
        $b->setRenderer("SGML");
        $b->setIndent(new Indent(0, "  ", Indent::LF));
        $h2   = new Helper($b, array("meta", "input", "br"));
        $obj2 = TestUtil::createTestHelperObject($h2);
        $this->assertSame(TestUtil::getCustomBuildResult(), $obj2->write());
    }
    
    /**
     * このオブジェクトがラップしているノードが DebugBuilder によって build() されることを確認します.
     * @covers Peach\Markup\HelperObject::debug
     */
    public function testDebug()
    {
        $h1       = $this->helper;
        $obj1     = TestUtil::createTestHelperObject($h1);
        $expected = TestUtil::getDebugBuildResult();
        $this->expectOutputString($expected);
        $this->assertSame($expected, $obj1->debug());
    }
    
    /**
     * prototype() のテストです.
     * 返り値の HelperObject が以下の Component をラップしていることを確認します.
     * 
     * - ラップしているノードが ContainerElement だった場合, 同じ属性を持つ空の ContainerElement
     * - ラップしているノードが EmptyElement だった場合, 同じ属性を持つ EmptyElement
     * - それ以外は, 空の NodeList
     * 
     * @covers Peach\Markup\HelperObject::prototype
     * @covers Peach\Markup\HelperObject::createPrototype
     */
    public function testPrototype()
    {
        $h = $this->helper;
        
        $e1   = new ContainerElement("div");
        $e1->setAttributes(array("id" => "sample", "class" => "test"));
        $obj1 = new HelperObject($h, "div");
        $obj1->attr("id", "sample")->attr("class", "test")->append("First")->append("Second")->append("Third");
        $this->assertEquals($e1, $obj1->prototype()->getNode());
        
        $e2   = new EmptyElement("input");
        $e2->setAttributes(array("type" => "text", "name" => "subject", "value" => ""));
        $obj2 = new HelperObject($h, "input");
        $obj2->attr(array("type" => "text", "name" => "subject", "value" => ""));
        $this->assertEquals($e2, $obj2->prototype()->getNode());
        
        $nl   = new NodeList();
        $text = new Text("This is Test");
        $obj3 = new HelperObject($h, $text);
        $this->assertEquals($nl, $obj3->prototype()->getNode());
    }
    
    /**
     * このオブジェクトがラップしているノードの accept() が実行されることを確認します.
     * @covers Peach\Markup\HelperObject::accept
     */
    public function testAccept()
    {
        $h     = $this->helper;
        $obj   = new HelperObject($h, "div");
        $debug = new DebugContext(false);
        $obj->accept($debug);
        $this->assertSame("ContainerElement(div) {\r\n}\r\n", $debug->getResult());
    }
    
    /**
     * getChildNodes() のテストです. 以下を確認します.
     * 
     * - このオブジェクトがラップしているノードが Container だった場合はそのノードの childNodes() の結果を返すこと
     * - それ以外は空配列を返すこと
     * 
     * @covers Peach\Markup\HelperObject::getChildNodes
     */
    public function testGetChildNodes()
    {
        $h        = $this->helper;
        $expected = array(
            new Text("First"),
            new Text("Second"),
            new Text("Third"),
        );
        
        $p        = new ContainerElement("p");
        $p->appendNode("First");
        $p->appendNode("Second");
        $p->appendNode("Third");
        $obj1     = new HelperObject($h, $p);
        $this->assertEquals($expected, $obj1->getChildNodes());
        
        $text     = new Text("This is test");
        $obj2     = new HelperObject($h, $text);
        $this->assertSame(array(), $obj2->getChildNodes());
    }
}
