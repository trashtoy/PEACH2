<?php
namespace Peach\Markup;
require_once(__DIR__ . "/ContextTest.php");
require_once(__DIR__ . "/TestUtil.php");

class DebugContextTest extends ContextTest
{
    /**
     * @var DebugContext
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setup();
        $this->object = new DebugContext();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 引数に true を指定して初期化した場合に自動で echo が行われることを確認します.
     * 
     * @covers Peach\Markup\DebugContext::__construct
     */
    public function test__constructByEchoModeOn()
    {
        $obj = new DebugContext(true);
        $obj->handleText(new Text("foobar"));
        $this->assertSame("Text\r\n", $this->getActualOutput());
    }
    
    /**
     * 引数に false を指定して初期化した場合は echo が行われないことを確認します.
     * 
     * @covers Peach\Markup\DebugContext::__construct
     */
    public function test__constructByEchoModeOff()
    {
        $obj = new DebugContext(false);
        $obj->handleText(new Text("foobar"));
        $this->assertFalse($this->hasOutput());
    }
    
    /**
     * getResult() のテストです.
     * 各ノードのクラス名とその入れ子構造が出力されることを確認します.
     * 
     * @covers Peach\Markup\DebugContext::getResult
     */
    public function testGetResult()
    {
        $expected = TestUtil::getDebugBuildResult();
        $node     = TestUtil::getTestNode();
        $context  = $this->object;
        $this->expectOutputString($expected);
        $context->handle($node);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleComment() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleComment
     * @covers Peach\Markup\DebugContext::startNode
     * @covers Peach\Markup\DebugContext::handleContainer
     * @covers Peach\Markup\DebugContext::endNode
     */
    public function testHandleComment()
    {
        $expected = implode("\r\n", array(
            "Comment {",
            "    Text",
            "}",
        )) . "\r\n";
        $context   = $this->object;
        $comment   = new Comment();
        $comment->append("This is test");
        $this->expectOutputString($expected);
        $context->handleComment($comment);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleContainerElement() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleContainerElement
     * @covers Peach\Markup\DebugContext::startNode
     * @covers Peach\Markup\DebugContext::handleContainer
     * @covers Peach\Markup\DebugContext::endNode
     */
    public function testHandleContainerElement()
    {
        $expected = implode("\r\n", array(
            "ContainerElement(div) {",
            "    Text",
            "}",
        )) . "\r\n";
        $context   = $this->object;
        $container = new ContainerElement("div");
        $container->append("This is test");
        $this->expectOutputString($expected);
        $context->handleContainerElement($container);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleEmptyElement() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleEmptyElement
     * @covers Peach\Markup\DebugContext::append
     */
    public function testHandleEmptyElement()
    {
        $expected = "EmptyElement(br)\r\n";
        $context  = $this->object;
        $emp      = new EmptyElement("br");
        $this->expectOutputString($expected);
        $context->handleEmptyElement($emp);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleNodeList() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleNodeList
     * @covers Peach\Markup\DebugContext::startNode
     * @covers Peach\Markup\DebugContext::handleContainer
     * @covers Peach\Markup\DebugContext::endNode
     */
    public function testHandleNodeList()
    {
        $expected = implode("\r\n", array(
            "NodeList {",
            "    Comment {",
            "        Text",
            "    }",
            "    EmptyElement(img)",
            "    Text",
            "}",
        )) . "\r\n";
        $nodeList = new NodeList();
        $comment  = new Comment();
        $comment->append("This is test comment");
        $img      = new EmptyElement("img");
        $img->setAttributes(array("src" => "test.jpg", "alt" => ""));
        $nodeList->append($comment);
        $nodeList->append($img);
        $nodeList->append("Test image");
        
        $context = $this->object;
        $this->expectOutputString($expected);
        $context->handleNodeList($nodeList);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleText() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleText
     * @covers Peach\Markup\DebugContext::append
     */
    public function testHandleText()
    {
        $expected = "Text\r\n";
        $context  = $this->object;
        $text     = new Text("This is test");
        $this->expectOutputString($expected);
        $context->handleText($text);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleCode() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleCode
     * @covers Peach\Markup\DebugContext::append
     */
    public function testHandleCode()
    {
        $expected = "Code\r\n";
        $context  = $this->object;
        $str      = implode("\n", array(
            "$(function() {",
            "    $(\"#navi\").hide();",
            "}",
        ));
        $code     = new Code($str);
        $this->expectOutputString($expected);
        $context->handleCode($code);
        $this->assertSame($expected, $context->getResult());
    }
    
    /**
     * handleNone() のテストです.
     * 
     * @covers Peach\Markup\DebugContext::handleNone
     * @covers Peach\Markup\DebugContext::append
     */
    public function testHandleNone()
    {
        $expected = "None\r\n";
        $context  = $this->object;
        $none     = None::getInstance();
        $this->expectOutputString($expected);
        $context->handleNone($none);
        $this->assertSame($expected, $context->getResult());
    }
}
