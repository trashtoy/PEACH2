<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestUtil.php");
use Peach\DT\Datetime;

class BaseHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseHelper
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new BaseHelper(new DefaultBuilder(), array("meta", "input", "br"));
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * tag() のテストです. 以下について確認します.
     * 
     * - HelperObject 型のオブジェクトを返すこと
     * - 返り値の HelperObject が, createNode() によって生成された Component をラップしていること
     * - 第 2 引数を指定した場合, 生成される HelperObject の要素にその内容が属性としてセットされること
     * - 第 2 引数を指定したが, 生成される HelperObject が要素ではない場合は無視されること
     * 
     * @covers Peach\Markup\BaseHelper::tag
     */
    public function testCreateObject()
    {
        $h    = $this->object;
        $obj  = $h->tag("p");
        $this->assertInstanceOf("Peach\\Markup\\HelperObject", $obj);
        
        $h1   = $h->tag(new ContainerElement("h1"));
        $div  = $h->tag("div");
        $emp  = $h->tag("");
        $this->assertEquals(new ContainerElement("h1"),  $h1->getNode());
        $this->assertEquals(new ContainerElement("div"), $div->getNode());
        $this->assertEquals(new NodeList(),              $emp->getNode());
        
        $obj2  = $h->tag("input", array("type" => "text", "name" => "title", "value" => "sample attribute"));
        $input = new EmptyElement("input");
        $input->setAttributes(array("type" => "text", "name" => "title", "value" => "sample attribute"));
        $this->assertEquals($input, $obj2->getNode());
        
        $obj3  = $h->tag(null, array("class" => "ignore_test"));
        $this->assertEquals(new NodeList(), $obj3->getNode());
    }
     
    /**
     * createElement() のテストです.
     * コンストラクタ引数で空要素タグとして指定したタグ名については EmptyElement,
     * それ以外については ContainerElement 型オブジェクトを返すことを確認します.
     * 
     * @param        string $expectedClassName
     * @param        string $tagName
     * @covers       Peach\Markup\BaseHelper::createElement
     * @dataProvider forTestCreateElement
     */
    public function testCreateElement($expectedClassName, $tagName)
    {
        $h   = $this->object;
        $obj = $h->createElement($tagName);
        $this->assertInstanceOf($expectedClassName, $obj);
    }
    
    /**
     * createElement() のテストのためのデータセットです.
     * 2 番目の引数で createElement() を実行した結果,
     * 1 番目のクラス名のインスタンスが生成されることを期待します.
     * 
     * @return array
     */
    public function forTestCreateElement()
    {
        return array(
            array("Peach\\Markup\\EmptyElement", "meta"),
            array("Peach\\Markup\\EmptyElement", "input"),
            array("Peach\\Markup\\EmptyElement", "br"),
            array("Peach\\Markup\\ContainerElement", "body"),
            array("Peach\\Markup\\ContainerElement", "p"),
            array("Peach\\Markup\\ContainerElement", "div"),
        );
    }

    /**
     * Helper にセットされた Builder の build() の結果が返ることを確認します.
     * @covers Peach\Markup\BaseHelper::write
     */
    public function testWrite()
    {
        $h = $this->object;
        $o = $h->tag(TestUtil::getTestNode());
        $this->assertSame(TestUtil::getDefaultBuildResult(), $h->write($o));
    }
    
    /**
     * getBuilder() と setBuilder() のテストです. 以下について確認します.
     * 
     * - getBuilder() がコンストラクタの引数に指定した Builder オブジェクトと同一のものを返すこと
     * - setBuilder() で指定した Builder オブジェクトが getBuilder() から取得できること
     * 
     * @covers Peach\Markup\BaseHelper::__construct
     * @covers Peach\Markup\BaseHelper::getBuilder
     * @covers Peach\Markup\BaseHelper::setBuilder
     */
    public function testAccessBuilder()
    {
        $b1 = new DefaultBuilder();
        $b1->setIndent(new Indent(0, Indent::TAB, Indent::LF));
        $b2 = new DefaultBuilder();
        $b2->setBreakControl(MinimalBreakControl::getInstance());
        
        $h = new BaseHelper($b1);
        $this->assertSame($b1, $h->getBuilder());
        $h->setBuilder($b2);
        $this->assertSame($b2, $h->getBuilder());
    }
}
