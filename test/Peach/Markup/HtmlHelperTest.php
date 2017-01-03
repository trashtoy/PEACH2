<?php
namespace Peach\Markup;

require_once(__DIR__ . "/AbstractHelperTest.php");

class HtmlHelperTest extends AbstractHelperTest
{
    /**
     * @var HtmlHelper
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $emptyList    = array("area", "base", "basefont", "br", "col", "embed", "frame", "hr", "img", "input", "isindex", "keygen", "link", "meta", "param", "source", "track", "wbr");
        $breakControl = new NameBreakControl(
            array("html", "head", "body", "ul", "ol", "dl", "table"),
            array("pre", "code", "textarea")
        );
        
        $htmlBuilder = new DefaultBuilder();
        $htmlBuilder->setBreakControl($breakControl);
        $htmlBuilder->setRenderer(SgmlRenderer::getInstance());
        
        $xhtmlBuilder = new DefaultBuilder();
        $xhtmlBuilder->setBreakControl($breakControl);
        $xhtmlBuilder->setRenderer(XmlRenderer::getInstance());
        
        $this->htmlBaseHelper  = new BaseHelper($htmlBuilder, $emptyList);
        $this->xhtmlBaseHelper = new BaseHelper($xhtmlBuilder, $emptyList);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 引数に指定されたモードに応じて, 適切なインスタンスを生成することを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::newInstance
     * @covers Peach\Markup\HtmlHelper::detectMode
     * @covers Peach\Markup\HtmlHelper::checkXhtmlFromMode
     * @covers Peach\Markup\HtmlHelper::getDocTypeFromMode
     * @covers Peach\Markup\HtmlHelper::createBaseHelper
     * @covers Peach\Markup\HtmlHelper::createBuilder
     * @covers Peach\Markup\HtmlHelper::getEmptyNodeNames
     * @covers Peach\Markup\HtmlHelper::__construct
     * @covers Peach\Markup\AbstractHelper::__construct
     * @dataProvider forTestNewInstance
     */
    public function testNewInstance(HtmlHelper $expected, $mode)
    {
        $this->assertEquals($expected, HtmlHelper::newInstance($mode));
    }
    
    /**
     * testNewInstance() のデータセットです.
     * 引数に指定されたモードは, 以下のように扱われます
     * 
     * - HtmlHelper で定義されている各定数: それぞれの定数に対応するインスタンス
     * - null, 空文字列, "html": HTML5 として扱う
     * - "html4": HTML4.01 Transitional として扱う
     * - "xhtml": XHTML1.0 Transitional として扱う
     * - 大文字・小文字は区別しない
     * 
     * @return array
     */
    public function forTestNewInstance()
    {
        list($htmlBase, $xhtmlBase) = $this->createBaseHelper();
        $html4s    = new HtmlHelper($htmlBase,  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">', false);
        $html4t    = new HtmlHelper($htmlBase,  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">', false);
        $xhtml1s   = new HtmlHelper($xhtmlBase, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">', true);
        $xhtml1t   = new HtmlHelper($xhtmlBase, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', true);
        $xhtml1_1  = new HtmlHelper($xhtmlBase, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">', true);
        $html5     = new HtmlHelper($htmlBase,  '<!DOCTYPE html>', false);
        return array(
            array($html4s, HtmlHelper::MODE_HTML4_STRICT),
            array($html4t, HtmlHelper::MODE_HTML4_TRANSITIONAL),
            array($xhtml1s, HtmlHelper::MODE_XHTML1_STRICT),
            array($xhtml1t, HtmlHelper::MODE_XHTML1_TRANSITIONAL),
            array($xhtml1_1, HtmlHelper::MODE_XHTML1_1),
            array($html5, HtmlHelper::MODE_HTML5),
            array($html5, ""),
            array($html5, "html"),
            array($html5, "HTML"),
            array($html4t, "HTML4"),
            array($xhtml1t, "xhtml"),
        );
    }
    
    /**
     * newInstance() の引数に不正な値を指定した際に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::newInstance
     * @covers Peach\Markup\HtmlHelper::detectMode
     * @expectedException InvalidArgumentException
     */
    public function testNewInstanceFailByInvalidMode()
    {
        HtmlHelper::newInstance("invalidname");
    }
    
    /**
     * XHTML モードでインスタンスを生成した場合に
     * xmlDec() の返り値が XML 宣言を出力することを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::xmlDec
     * @dataProvider forTestXmlDecReturnsCode
     */
    public function testXmlDecReturnsCode($mode)
    {
        $obj     = HtmlHelper::newInstance($mode);
        $subject = $obj->xmlDec();
        $this->assertInstanceOf("Peach\\Markup\\Code", $subject);
        $this->assertSame('<?xml version="1.0" encoding="UTF-8"?>', $subject->getText());
    }
    
    /**
     * testXmlDecReturnsCode() のデータセットです.
     * 
     * @return array
     */
    public function forTestXmlDecReturnsCode()
    {
        return array(
            array(HtmlHelper::MODE_XHTML1_STRICT),
            array(HtmlHelper::MODE_XHTML1_TRANSITIONAL),
            array(HtmlHelper::MODE_XHTML1_1),
        );
    }
    
    /**
     * HTML モードでインスタンスを生成した場合に
     * xmlDec() の返り値が None となることを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::xmlDec
     * @dataProvider forTestXmlDecReturnsNone
     */
    public function testXmlDecReturnsNone($mode)
    {
        $obj     = HtmlHelper::newInstance($mode);
        $subject = $obj->xmlDec();
        $this->assertInstanceOf("Peach\\Markup\\None", $subject);
    }
    
    /**
     * testXmlDecReturnsNone() のデータセットです.
     * 
     * @return array
     */
    public function forTestXmlDecReturnsNone()
    {
        return array(
            array(HtmlHelper::MODE_HTML4_STRICT),
            array(HtmlHelper::MODE_HTML4_TRANSITIONAL),
            array(HtmlHelper::MODE_HTML5),
        );
    }
    
    /**
     * コンストラクタに設定した文書型宣言をあらわす Code オブジェクトを返すことを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::docType
     */
    public function testDocType()
    {
        $base    = new BaseHelper(new DefaultBuilder(), array());
        $obj     = new HtmlHelper($base, "<!DOCTYPE test>", false);
        $subject = $obj->docType();
        $this->assertInstanceOf("Peach\\Markup\\Code", $subject);
        $this->assertSame("<!DOCTYPE test>", $subject->getText());
    }
    
    /**
     * コンストラクタで $docType に空文字列を指定した場合に
     * docType() が None オブジェクトを返すことを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::docType
     */
    public function testDocTypeReturnsNone()
    {
        $base    = new BaseHelper(new DefaultBuilder(), array());
        $obj     = new HtmlHelper($base, "", false);
        $subject = $obj->docType();
        $this->assertInstanceOf("Peach\\Markup\\None", $subject);
    }
    
    /**
     * comment() のテストです. 以下を確認します.
     * 
     * - 返り値の HelperObject が Comment ノードをラップしていること
     * - 第 1 引数に指定した値が, ラップしている Comment オブジェクトの子ノードになること
     * - 第 1 引数を省略した場合は空の Comment ノードが生成されること
     * - 第 2, 第3 引数でコメントの接頭辞・接尾辞を指定できること
     * 
     * @covers Peach\Markup\HtmlHelper::comment
     */
    public function testComment()
    {
        $obj = HtmlHelper::newInstance();
        $c1  = $obj->comment("SAMPLE COMMENT");
        $this->assertInstanceOf("Peach\\Markup\\Comment", $c1->getNode());
        $this->assertSame("<!--SAMPLE COMMENT-->", $c1->write());
        
        $div = $obj->tag("div")
                ->append($obj->tag("h1")->append("TEST"))
                ->append($obj->tag("p")->append("The Quick Brown Fox Jumps Over The Lazy Dogs"));
        $ex1 = implode("\r\n", array(
            "<!--",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "-->",
        ));
        $c2  = $obj->comment($div);
        $this->assertSame($ex1, $c2->write());
        
        $c3  = $obj->comment();
        $this->assertSame("<!---->", $c3->write());
        $ex2 = implode("\r\n", array(
            "<!--[start]",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "[end]-->",
        ));
        $c4  = $obj->comment($div, "[start]", "[end]");
        $this->assertSame($ex2, $c4->write());
    }

    /**
     * conditionalComment() のテストです. 以下を確認します.
     * 
     * - 第 1 引数に指定された条件付きコメントの中に, 第 2 引数に定された文字列またはノードが含まれること
     * - 第 2 引数を省略した場合, 空の条件付きコメントが生成されること
     * 
     * @covers Peach\Markup\HtmlHelper::conditionalComment
     */
    public function testConditionalComment()
    {
        $obj = HtmlHelper::newInstance();
        $ex1 = "<!--[if lt IE 7]>He died on April 9, 2014.<![endif]-->";
        $c1  = $obj->conditionalComment("lt IE 7", "He died on April 9, 2014.");
        $this->assertSame($ex1, $c1->write());
        
        $ex2 = implode("\r\n", array(
            "<!--[if IE 9]>",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "<![endif]-->",
        ));
        $div = $obj->tag("div")
                ->append($obj->tag("h1")->append("TEST"))
                ->append($obj->tag("p")->append("The Quick Brown Fox Jumps Over The Lazy Dogs"));
        $c2  = $obj->conditionalComment("IE 9", $div);
        $this->assertSame($ex2, $c2->write());
        
        $ex3 = "<!--[if IE]><![endif]-->";
        $c3  = $obj->conditionalComment("IE");
        $this->assertSame($ex3, $c3->write());
    }

    /**
     * createSelectElement() のテストです.
     * 以下を確認します.
     * 
     * - 第 1 引数をデフォルト選択肢, 第 2 引数を項目の一覧とする select 要素を返すこと
     * - 第 2 引数の値に配列が含まれる場合, その項目が optgroup になること
     * - 第 3 引数で select 要素の属性を指定できること
     * 
     * @covers Peach\Markup\HtmlHelper::createSelectElement
     * @covers Peach\Markup\HtmlHelper::createOptions
     */
    public function testCreateSelectElement()
    {
        $obj        = HtmlHelper::newInstance();
        $candidates = array(
            "Apple"  => 1,
            "Orange" => 2,
            "Pear"   => 3,
            "Peach"  => 4,
        );
        $select1    = $obj->createSelectElement(null, $candidates);
        $this->assertEquals($this->createSampleSelectNode(false, false), $select1);
        $select2    = $obj->createSelectElement("4", $candidates);
        $this->assertEquals($this->createSampleSelectNode(true, false),  $select2);
        $select3    = $obj->createSelectElement("", $candidates, array("id" => "test", "name" => "favorite"));
        $this->assertEquals($this->createSampleSelectNode(false, true), $select3);
        
        $candidates2 = array(
            "Fruit"   => $candidates,
            "Dessert" => array(
                "Chocolate" => 5,
                "Doughnut"  => 6,
                "Ice cream" => 7,
            ),
            "Others" => 8,
        );
        $select4    = $obj->createSelectElement(6, $candidates2);
        $this->assertEquals($this->createSampleSelectNode2(), $select4);
    }
    
    /**
     * @param  bool $selectFlag
     * @param  bool $attrFlag
     * @return ContainerElement
     */
    private function createSampleSelectNode($selectFlag, $attrFlag)
    {
        $select = new ContainerElement("select");
        
        $opt1   = new ContainerElement("option");
        $opt1->setAttribute("value", "1");
        $opt1->appendNode("Apple");
        $select->appendNode($opt1);
        
        $opt2   = new ContainerElement("option");
        $opt2->setAttribute("value", "2");
        $opt2->appendNode("Orange");
        $select->appendNode($opt2);
        
        $opt3   = new ContainerElement("option");
        $opt3->setAttribute("value", "3");
        $opt3->appendNode("Pear");
        $select->appendNode($opt3);
        
        $opt4   = new ContainerElement("option");
        $opt4->setAttribute("value", "4");
        $opt4->appendNode("Peach");
        $select->appendNode($opt4);
        if ($selectFlag) {
            $opt4->setAttribute("selected");
        }
        
        if ($attrFlag) {
            $select->setAttributes(array("id" => "test", "name" => "favorite"));
        }
        return $select;
    }
    
    /**
     * @return ContainerElement
     */
    private function createSampleSelectNode2()
    {
        $select = new ContainerElement("select");
        
        $grp1   = new ContainerElement("optgroup");
        $grp1->setAttribute("label", "Fruit");
        $select->appendNode($grp1);
        $grp2   = new ContainerElement("optgroup");
        $grp2->setAttribute("label", "Dessert");
        $select->appendNode($grp2);
        $other  = new ContainerElement("option");
        $other->setAttribute("value", "8");
        $other->appendNode("Others");
        $select->appendNode($other);
        
        $opt1   = new ContainerElement("option");
        $opt1->setAttribute("value", "1");
        $opt1->appendNode("Apple");
        $grp1->appendNode($opt1);
        
        $opt2   = new ContainerElement("option");
        $opt2->setAttribute("value", "2");
        $opt2->appendNode("Orange");
        $grp1->appendNode($opt2);
        
        $opt3   = new ContainerElement("option");
        $opt3->setAttribute("value", "3");
        $opt3->appendNode("Pear");
        $grp1->appendNode($opt3);
        
        $opt4   = new ContainerElement("option");
        $opt4->setAttribute("value", "4");
        $opt4->appendNode("Peach");
        $grp1->appendNode($opt4);
        
        $opt5   = new ContainerElement("option");
        $opt5->setAttribute("value", "5");
        $opt5->appendNode("Chocolate");
        $grp2->appendNode($opt5);
        
        $opt6   = new ContainerElement("option");
        $opt6->setAttribute("value", "6");
        $opt6->setAttribute("selected");
        $opt6->appendNode("Doughnut");
        $grp2->appendNode($opt6);
        
        $opt7   = new ContainerElement("option");
        $opt7->setAttribute("value", "7");
        $opt7->appendNode("Ice cream");
        $grp2->appendNode($opt7);
        
        return $select;
    }
    
    /**
     * select() のテストです. createSelectElement() の結果をラップした
     * HelperObject を返すことを確認します.
     * 
     * @covers Peach\Markup\HtmlHelper::select
     * @covers Peach\Markup\HtmlHelper::createOptions
     */
    public function testSelect()
    {
        $obj        = HtmlHelper::newInstance();
        $candidates = array(
            "Fruit"   => array(
                "Apple"  => 1,
                "Orange" => 2,
                "Pear"   => 3,
                "Peach"  => 4,
            ),
            "Dessert" => array(
                "Chocolate" => 5,
                "Doughnut"  => 6,
                "Ice cream" => 7,
            ),
            "Others" => 8,
        );
        $expected = implode("\r\n", array(
            '<select name="favorite">',
            '    <optgroup label="Fruit">',
            '        <option value="1">Apple</option>',
            '        <option value="2">Orange</option>',
            '        <option value="3">Pear</option>',
            '        <option value="4">Peach</option>',
            '    </optgroup>',
            '    <optgroup label="Dessert">',
            '        <option value="5">Chocolate</option>',
            '        <option value="6" selected>Doughnut</option>',
            '        <option value="7">Ice cream</option>',
            '    </optgroup>',
            '    <option value="8">Others</option>',
            '</select>',
        ));
        $select = $obj->select(6, $candidates, array("name" => "favorite"));
        $this->assertInstanceOf("Peach\\Markup\\HelperObject", $select);
        $this->assertSame($expected, $select->write());
    }
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::createElement
     */
    public function testCreateElement()
    {
        list($htmlBase, $xhtmlBase) = $this->createBaseHelper();
        $obj1 = new HtmlHelper($htmlBase, "", false);
        $obj2 = new HtmlHelper($xhtmlBase, "", true);
        $this->assertEquals(
                $htmlBase->createElement("img", array("src" => "test.png")),
                $obj1->createElement("img", array("src" => "test.png"))
            );
        $this->assertEquals(
                $xhtmlBase->createElement("img", array("src" => "foobar.jpg")),
                $obj2->createElement("img", array("src" => "foobar.jpg"))
            );
    }
    
    /**
     * コンストラクタで指定した Helper と同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::getParentHelper
     */
    public function testGetParentHelper()
    {
        list($htmlBase, $xhtmlBase) = $this->createBaseHelper();
        $obj1 = new HtmlHelper($htmlBase, "", false);
        $obj2 = new HtmlHelper($xhtmlBase, "", true);
        $this->assertSame($htmlBase, $obj1->getParentHelper());
        $this->assertSame($xhtmlBase, $obj2->getParentHelper());
    }
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::tag
     */
    public function testTag()
    {
        list($htmlBase, $xhtmlBase) = $this->createBaseHelper();
        $obj1 = new HtmlHelper($htmlBase, "", false);
        $obj2 = new HtmlHelper($xhtmlBase, "", true);
        $div1 = $htmlBase->tag("div", array("class" => "test"));
        $div2 = $xhtmlBase->tag("div", array("class" => "test"));
        $this->assertEquals($div1, $obj1->tag("div", array("class" => "test")));
        $this->assertEquals($div2, $obj2->tag("div", array("class" => "test")));
    }
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::write
     */
    public function testWrite()
    {
        list($htmlBase, $xhtmlBase) = $this->createBaseHelper();
        $obj1 = new HtmlHelper($htmlBase, "", false);
        $obj2 = new HtmlHelper($xhtmlBase, "", true);
        
        $test = $htmlBase->tag("p");
        $test->appendNode("This is test.");
        $test->appendNode(new EmptyElement("br"));
        $span = new ContainerElement("span");
        $span->appendNode("hogehoge");
        $test->appendNode($span);
        
        $result1 = $htmlBase->write($test);
        $result2 = $xhtmlBase->write($test);
        $this->assertSame($result1, $obj1->write($test));
        $this->assertSame($result2, $obj2->write($test));
    }
    
    /**
     * @return array
     */
    private function createBaseHelper()
    {
        $emptyList    = array("area", "base", "basefont", "br", "col", "embed", "frame", "hr", "img", "input", "isindex", "keygen", "link", "meta", "param", "source", "track", "wbr");
        $breakControl = new NameBreakControl(
            array("html", "head", "body", "ul", "ol", "dl", "table"),
            array("pre", "code", "textarea")
        );
        
        $htmlBuilder = new DefaultBuilder();
        $htmlBuilder->setBreakControl($breakControl);
        $htmlBuilder->setRenderer(SgmlRenderer::getInstance());
        
        $xhtmlBuilder = new DefaultBuilder();
        $xhtmlBuilder->setBreakControl($breakControl);
        $xhtmlBuilder->setRenderer(XmlRenderer::getInstance());
        
        return array(
            new BaseHelper($htmlBuilder, $emptyList),
            new BaseHelper($xhtmlBuilder, $emptyList),
        );
    }
}
