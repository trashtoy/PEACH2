<?php
namespace Peach\Markup;
require_once(__DIR__ . "/ContextTest.php");
require_once(__DIR__ . "/TestUtil.php");

class DefaultContextTest extends ContextTest
{
    /**
     * @var DefaultContext
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = $this->getTestObject();
    }
    
    /**
     * @return \Peach\Markup\DefaultContext
     */
    private function getTestObject()
    {
        return new DefaultContext(XmlRenderer::getInstance());
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * handleComment のテストです. 以下を確認します.
     * 
     * - ノードが 1 つの場合 "<!--comment text-->" 形式になること
     * - ノードが複数の場合, コメントが改行されること
     * - 接頭辞および接尾辞が指定された場合は改行されること
     * 
     * @covers Peach\Markup\DefaultContext::handleComment
     * @covers Peach\Markup\DefaultContext::checkBreakModeInComment
     * @covers Peach\Markup\DefaultContext::formatChildNodes
     * @covers Peach\Markup\DefaultContext::breakCode
     * @covers Peach\Markup\DefaultContext::escapeEndComment
     */
    public function testHandleComment()
    {
        $expected1 = "<!---->";
        $comment   = new Comment();
        $obj1      = $this->object;
        $obj1->handleComment($comment);
        $this->assertSame($expected1, $obj1->getResult());
        
        $expected2 = "<!--SAMPLE TEXT-->";
        $comment->append("SAMPLE TEXT");
        $obj2      = $this->getTestObject();
        $obj2->handleComment($comment);
        $this->assertSame($expected2, $obj2->getResult());
        
        $expected3 = implode("\r\n", array(
            "<!--",
            "SAMPLE TEXT",
            "<element />",
            "-->",
        ));
        $comment->append(new EmptyElement("element"));
        $obj3      = $this->getTestObject();
        $obj3->handleComment($comment);
        $this->assertSame($expected3, $obj3->getResult());
        
        $expected4 = implode("\r\n", array(
            '<!--[if IE 9]>',
            '<script src="sample.js"></script>',
            '<![endif]-->'
        ));
        $script    = new ContainerElement("script");
        $script->setAttribute("src", "sample.js");
        $comment2  = new Comment("[if IE 9]>", "<![endif]");
        $comment2->append($script);
        $obj4      = $this->getTestObject();
        $obj4->handleComment($comment2);
        $this->assertSame($expected4, $obj4->getResult());
    }
    
    /**
     * handleText のテストです. 以下を確認します.
     * 
     * - 改行コード "\r", "\n", "\r\n" が文字参照 "&#xa;" に置換されること
     * - 特殊文字がエスケープされること
     * 
     * @covers Peach\Markup\DefaultContext::handleText
     * @covers Peach\Markup\DefaultContext::indent
     * @covers Peach\Markup\DefaultContext::escape
     */
    public function testHandleText()
    {
        $text  = new Text("THIS IS SAMPLE");
        $obj1  = $this->object;
        $obj1->handleText($text);
        $this->assertSame("THIS IS SAMPLE", $obj1->getResult());
        
        $lines = new Text("FIRST\nSECOND\rTHIRD\r\nFOURTH\n\rFIFTH");
        $obj2  = $this->getTestObject();
        $obj2->handleText($lines);
        $this->assertSame("FIRST&#xa;SECOND&#xa;THIRD&#xa;FOURTH&#xa;&#xa;FIFTH", $obj2->getResult());
        
        $text2 = new Text("TEST & <!-- <script>alert(0);</script> -->");
        $obj3  = $this->getTestObject();
        $obj3->handleText($text2);
        $this->assertSame("TEST &amp; &lt;!-- &lt;script&gt;alert(0);&lt;/script&gt; --&gt;", $obj3->getResult());
    }
    
    /**
     * 指定されたコードがインデントされた状態でマークアップされることを確認します.
     * 
     * @covers Peach\Markup\DefaultContext::handleCode
     * @covers Peach\Markup\DefaultContext::indent
     * @covers Peach\Markup\DefaultContext::breakCode
     */
    public function testHandleCode()
    {
        $text = <<<EOS
body {
    color            : #000;
    background-color : #fff;
    width            : 750px;
}
@media(min-width : 1024px) {
    body {
        width : 1000px;
    }
}
EOS;
        $code  = new Code($text);
        $style = new ContainerElement("style");
        $style->setAttribute("type", "text/css");
        $style->append($code);
        $head  = new ContainerElement("head");
        $head->append($style);
        
        $expected = implode("\r\n", array(
            '<head>',
            '    <style type="text/css">',
            '        body {',
            '            color            : #000;',
            '            background-color : #fff;',
            '            width            : 750px;',
            '        }',
            '        @media(min-width : 1024px) {',
            '            body {',
            '                width : 1000px;',
            '            }',
            '        }',
            '    </style>',
            '</head>',
        ));
        $this->object->handle($head);
        $this->assertSame($expected, $this->object->getResult());
    }
    
    /**
     * handleEmptyElement のテストです. 以下を確認します.
     * 
     * - SGML 形式の場合 "<tagName>" となること
     * - XML 形式の場合 "<tagName />" となること
     * 
     * @covers Peach\Markup\DefaultContext::handleEmptyElement
     * @covers Peach\Markup\DefaultContext::indent
     */
    public function testHandleEmptyElement()
    {
        $input     = new EmptyElement("input");
        $input->setAttributes(array("type" => "checkbox", "name" => "flag", "value" => 1));
        $input->setAttribute("checked");
        
        $expected1 = '<input type="checkbox" name="flag" value="1" checked="checked" />';
        $obj1      = new DefaultContext(XmlRenderer::getInstance());
        $obj1->handleEmptyElement($input);
        $this->assertSame($expected1, $obj1->getResult());
        
        $expected2 = '<input type="checkbox" name="flag" value="1" checked>';
        $obj2      = new DefaultContext(SgmlRenderer::getInstance());
        $obj2->handleEmptyElement($input);
        $this->assertSame($expected2, $obj2->getResult());
    }
    
    /**
     * ContainerElement のテストです. 以下を確認します.
     * 
     * - 要素数が 0 の時は "<tagName></tagName>" 形式
     * - 要素数が 1 の時は "<tagName>NODE</tagName>" 形式
     * - 複数のノードを持つ子孫ノードが存在する場合は, 改行してインデントすること
     * 
     * @covers Peach\Markup\DefaultContext::handleContainerElement
     * @covers Peach\Markup\DefaultContext::formatChildNodes
     * @covers Peach\Markup\DefaultContext::breakCode
     */
    public function testHandleContainerElement()
    {
        $expected1 = "<sample></sample>";
        $node1     = new ContainerElement("sample");
        $obj1      = $this->object;
        $obj1->handleContainerElement($node1);
        $this->assertSame($expected1, $obj1->getResult());
        
        $expected2 = "<sample><empty /></sample>";
        $node2     = new ContainerElement("sample");
        $node2->append(new EmptyElement("empty"));
        $obj2      = $this->getTestObject();
        $obj2->handleContainerElement($node2);
        $this->assertSame($expected2, $obj2->getResult());
        
        $expected3 = "<sample><test></test></sample>";
        $node3     = new ContainerElement("sample");
        $child     = new ContainerElement("test");
        $node3->append($child);
        $obj3      = $this->getTestObject();
        $obj3->handleContainerElement($node3);
        $this->assertSame($expected3, $obj3->getResult());
        
        $expected4 = "<sample><test>NODE1</test></sample>";
        $child->append("NODE1");
        $obj4      = $this->getTestObject();
        $obj4->handleContainerElement($node3);
        $this->assertSame($expected4, $obj4->getResult());
        
        $expected5 = implode("\r\n", array(
            "<sample>",
            "    <test>",
            "        NODE1",
            "        NODE2",
            "    </test>",
            "</sample>",
        ));
        $child->append("NODE2");
        $obj5      = $this->getTestObject();
        $obj5->handleContainerElement($node3);
        $this->assertSame($expected5, $obj5->getResult());
    }

    /**
     * NodeList に含まれる各子ノードを handle することを確認します.
     * 
     * @covers Peach\Markup\DefaultContext::handleNodeList
     * @covers Peach\Markup\DefaultContext::formatChildNodes
     * @covers Peach\Markup\DefaultContext::breakCode
     */
    public function testHandleNodeList()
    {
        $node1    = new EmptyElement("empty");
        $node2    = new Text("Sample Text");
        $node3    = new ContainerElement("container");
        $node3->append("TEST");
        $nodeList = new NodeList();
        $nodeList->append($node1);
        $nodeList->append($node2);
        $nodeList->append($node3);
        $expected = implode("\r\n", array(
            '<empty />',
            'Sample Text',
            '<container>TEST</container>',
        ));
        $this->object->handleNodeList($nodeList);
        $this->assertSame($expected, $this->object->getResult());
    }
    
    /**
     * 結果に何も追加されないことを確認します。
     * 
     * @covers Peach\Markup\DefaultContext::handleNone
     */
    public function testHandleNone()
    {
        $none = None::getInstance();
        $obj  = $this->object;
        $obj->handle($none);
        $this->assertSame("", $obj->getResult());
    }
    
    /**
     * @covers Peach\Markup\DefaultContext::__construct
     * @covers Peach\Markup\DefaultContext::getResult
     */
    public function testGetResult()
    {
        $test    = TestUtil::getTestNode();
        $expect1 = TestUtil::getDefaultBuildResult();
        $obj1 = $this->object;
        $obj1->handle($test);
        $this->assertSame($expect1, $obj1->getResult());
        
        $expect2 = TestUtil::getCustomBuildResult();
        $indent = new Indent(0, "  ", Indent::LF);
        $obj2   = new DefaultContext(SgmlRenderer::getInstance(), $indent);
        $obj2->handle($test);
        $this->assertSame($expect2, $obj2->getResult());
    }
}
