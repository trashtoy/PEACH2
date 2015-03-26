<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestUtil.php");

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        Html::init();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * init() のテストです. 以下を確認します.
     * 
     * - 引数に true を指定した場合は XHTML 形式, false を指定した場合は HTML 形式のグローバル Helper が初期化されること
     * - 引数を省略した場合は HTML 形式で初期化されること
     * 
     * @covers Peach\Markup\Html::init
     * @covers Peach\Markup\Html::createHelper
     * @covers Peach\Markup\Html::createBuilder
     */
    public function testInit()
    {
        $xr = XmlRenderer::getInstance();
        $sr = SgmlRenderer::getInstance();
        
        Html::init(true);
        $b1 = Html::getBuilder();
        $this->assertSame($xr, $b1->getRenderer());
        
        Html::init(false);
        $b2 = Html::getBuilder();
        $this->assertSame($sr, $b2->getRenderer());
        
        Html::init();
        $b3 = Html::getBuilder();
        $this->assertSame($sr, $b3->getRenderer());
    }
    
    /**
     * getHelper() のテストです.
     * 
     * @covers Peach\Markup\Html::getHelper
     */
    public function testGetHelper()
    {
        $breakControl   = new NameBreakControl(
            array("html", "head", "body", "ul", "ol", "dl", "table"),
            array("pre", "code", "textarea")
        );
        $emptyNodeNames = array("area", "base", "basefont", "br", "col", "command", "embed", "frame", "hr", "img", "input", "isindex", "keygen", "link", "meta", "param", "source", "track", "wbr");
        
        $b1  = new DefaultBuilder();
        $b1->setBreakControl($breakControl);
        $b1->setRenderer("HTML");
        $ex1 = new Helper($b1, $emptyNodeNames);
        Html::init();
        $this->assertEquals($ex1, Html::getHelper());
        
        $b2  = new DefaultBuilder();
        $b2->setBreakControl($breakControl);
        $b2->setRenderer("XHTML");
        $ex2 = new Helper($b2, $emptyNodeNames);
        Html::init(true);
        $this->assertEquals($ex2, Html::getHelper());
    }
    
    /**
     * getBuilder() のテストです.
     * 返り値の Builder に対する変更が, グローバル Helper に適用されることを確認します.
     * 
     * @covers Peach\Markup\Html::getBuilder
     */
    public function testGetBuilder()
    {
        $builder = Html::getBuilder();
        $builder->setRenderer("html");
        $builder->setIndent(new Indent(0, "  ", Indent::LF));
        $result  = Html::tag(TestUtil::getTestNode())->write();
        $this->assertSame(TestUtil::getCustomBuildResult(), $result);
    }
    
    /**
     * tag() のテストです. 引数によって, 生成される HelperObject が以下の Component をラップすることを確認します.
     * 
     * - 文字列の場合: 引数を要素名に持つ Element
     * - null または引数なしの場合: 空の NodeList
     * - Node オブジェクトの場合: 引数の Node 自身
     * 
     * また, HTML4.01 および最新の HTML5 の勧告候補 (2014-02-04 時点) の仕様を元に,
     * 以下の要素が「空要素」として生成されることを確認します.
     * 
     * - HTML4.01: area, base, basefont, br, col, frame, hr, img, input, isindex, link, meta, param
     * - HTML5: area, base, br, col, command, embed, hr, img, input, keygen, link, meta, param, source, track, wbr
     * 
     * @covers Peach\Markup\Html::tag
     */
    public function testTag()
    {
        $containerExamples = array("html", "body", "div", "span");
        foreach ($containerExamples as $name) {
            $obj = Html::tag($name);
            $this->assertInstanceOf("Peach\\Markup\\ContainerElement", $obj->getNode());
        }
        
        $nodeList  = new NodeList();
        $obj2      = Html::tag(null);
        $this->assertEquals($nodeList, $obj2->getNode());
        $obj3      = Html::tag();
        $this->assertEquals($nodeList, $obj3->getNode());
        
        $text      = new Text("SAMPLE TEXT");
        $obj4      = Html::tag($text);
        $this->assertSame($text, $obj4->getNode());
        
        $emptyHtml4 = array("area", "base", "basefont", "br", "col", "frame", "hr", "img", "input", "isindex", "link", "meta", "param");
        $emptyHtml5 = array("area", "base", "br", "col", "command", "embed", "hr", "img", "input", "keygen", "link", "meta", "param", "source", "track", "wbr");
        $emptyList  = array_unique(array_merge($emptyHtml4, $emptyHtml5));
        foreach ($emptyList as $name) {
            $obj = Html::tag($name);
            $this->assertInstanceOf("Peach\\Markup\\EmptyElement", $obj->getNode());
        }
    }
    
    /**
     * comment() のテストです. 以下を確認します.
     * 
     * - 返り値の HelperObject が Comment ノードをラップしていること
     * - 第 1 引数に指定した値が, ラップしている Comment オブジェクトの子ノードになること
     * - 第 1 引数を省略した場合は空の Comment ノードが生成されること
     * - 第 2, 第3 引数でコメントの接頭辞・接尾辞を指定できること
     * 
     * @covers Peach\Markup\Html::comment
     */
    public function testComment()
    {
        $c1  = Html::comment("SAMPLE COMMENT");
        $this->assertInstanceOf("Peach\\Markup\\Comment", $c1->getNode());
        $this->assertSame("<!--SAMPLE COMMENT-->", $c1->write());
        
        $obj = Html::tag("div")
                ->append(Html::tag("h1")->append("TEST"))
                ->append(Html::tag("p")->append("The Quick Brown Fox Jumps Over The Lazy Dogs"));
        $ex1 = implode("\r\n", array(
            "<!--",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "-->",
        ));
        $c2  = Html::comment($obj);
        $this->assertSame($ex1, $c2->write());
        
        $c3  = Html::comment();
        $this->assertSame("<!---->", $c3->write());
        $ex2 = implode("\r\n", array(
            "<!--[start]",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "[end]-->",
        ));
        $c4  = Html::comment($obj, "[start]", "[end]");
        $this->assertSame($ex2, $c4->write());
    }
    
    /**
     * conditionalComment() のテストです. 以下を確認します.
     * 
     * - 第 1 引数に指定された条件付きコメントの中に, 第 2 引数に定された文字列またはノードが含まれること
     * - 第 2 引数を省略した場合, 空の条件付きコメントが生成されること
     *
     * @covers Peach\Markup\Html::conditionalComment
     */
    public function testConditionalComment()
    {
        $ex1 = "<!--[if lt IE 7]>He died on April 9, 2014.<![endif]-->";
        $c1  = Html::conditionalComment("lt IE 7", "He died on April 9, 2014.");
        $this->assertSame($ex1, $c1->write());
        
        $ex2 = implode("\r\n", array(
            "<!--[if IE 9]>",
            "<div>",
            "    <h1>TEST</h1>",
            "    <p>The Quick Brown Fox Jumps Over The Lazy Dogs</p>",
            "</div>",
            "<![endif]-->",
        ));
        $obj = Html::tag("div")
                ->append(Html::tag("h1")->append("TEST"))
                ->append(Html::tag("p")->append("The Quick Brown Fox Jumps Over The Lazy Dogs"));
        $c2  = Html::conditionalComment("IE 9", $obj);
        $this->assertSame($ex2, $c2->write());
        
        $ex3 = "<!--[if IE]><![endif]-->";
        $c3  = Html::conditionalComment("IE");
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
     * @covers Peach\Markup\Html::createSelectElement
     * @covers Peach\Markup\Html::createOptions
     */
    public function testCreateSelectElement()
    {
        $candidates = array(
            "Apple"  => 1,
            "Orange" => 2,
            "Pear"   => 3,
            "Peach"  => 4,
        );
        $select1    = Html::createSelectElement(null, $candidates);
        $this->assertEquals($this->createSampleSelectNode(false, false), $select1);
        $select2    = Html::createSelectElement("4", $candidates);
        $this->assertEquals($this->createSampleSelectNode(true, false),  $select2);
        $select3    = Html::createSelectElement("", $candidates, array("id" => "test", "name" => "favorite"));
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
        $select4    = Html::createSelectElement(6, $candidates2);
        $builder    = new DefaultBuilder();
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
        $opt1->append("Apple");
        $select->append($opt1);
        
        $opt2   = new ContainerElement("option");
        $opt2->setAttribute("value", "2");
        $opt2->append("Orange");
        $select->append($opt2);
        
        $opt3   = new ContainerElement("option");
        $opt3->setAttribute("value", "3");
        $opt3->append("Pear");
        $select->append($opt3);
        
        $opt4   = new ContainerElement("option");
        $opt4->setAttribute("value", "4");
        $opt4->append("Peach");
        $select->append($opt4);
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
        $select->append($grp1);
        $grp2   = new ContainerElement("optgroup");
        $grp2->setAttribute("label", "Dessert");
        $select->append($grp2);
        $other  = new ContainerElement("option");
        $other->setAttribute("value", "8");
        $other->append("Others");
        $select->append($other);
        
        $opt1   = new ContainerElement("option");
        $opt1->setAttribute("value", "1");
        $opt1->append("Apple");
        $grp1->append($opt1);
        
        $opt2   = new ContainerElement("option");
        $opt2->setAttribute("value", "2");
        $opt2->append("Orange");
        $grp1->append($opt2);
        
        $opt3   = new ContainerElement("option");
        $opt3->setAttribute("value", "3");
        $opt3->append("Pear");
        $grp1->append($opt3);
        
        $opt4   = new ContainerElement("option");
        $opt4->setAttribute("value", "4");
        $opt4->append("Peach");
        $grp1->append($opt4);
        
        $opt5   = new ContainerElement("option");
        $opt5->setAttribute("value", "5");
        $opt5->append("Chocolate");
        $grp2->append($opt5);
        
        $opt6   = new ContainerElement("option");
        $opt6->setAttribute("value", "6");
        $opt6->setAttribute("selected");
        $opt6->append("Doughnut");
        $grp2->append($opt6);
        
        $opt7   = new ContainerElement("option");
        $opt7->setAttribute("value", "7");
        $opt7->append("Ice cream");
        $grp2->append($opt7);
        
        return $select;
    }
    
    /**
     * select() のテストです. createSelectElement() の結果をラップした
     * HelperObject を返すことを確認します.
     * 
     * @covers Peach\Markup\Html::select
     * @covers Peach\Markup\Html::createOptions
     */
    public function testSelect()
    {
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
        $select = Html::select(6, $candidates, array("name" => "favorite"));
        $this->assertInstanceOf("Peach\\Markup\\HelperObject", $select);
        $this->assertSame($expected, $select->write());
    }
    
    /**
     * alias() のテストです. 以下を確認します.
     * 
     * - 引数を省略した場合は array("tag" => "tag") と同様の結果になること
     * - 引数に指定した内容でエイリアスを定義できること
     * 
     * @covers Peach\Markup\Html::alias
     * @covers Peach\Markup\Html::handleAlias
     */
    public function testAlias()
    {
        $this->assertFalse(function_exists("tag"));
        Html::alias(array());
        Html::alias();
        $this->assertTrue(function_exists("tag"));
        $this->assertSame("<p>Hello World!</p>", tag("p")->append("Hello World!")->write());

        $aliasList = array("t", "c", "cond", "s");
        foreach ($aliasList as $name) {
            $this->assertFalse(function_exists($name));
        }
        Html::alias(array("tag" => "t", "comment" => "c", "conditionalComment" => "cond", "select" => "s"));
        foreach ($aliasList as $name) {
            $this->assertTrue(function_exists($name));
        }
        $ex1 = implode("\r\n", array(
            '<div>',
            '    <!--TEST-->',
            '    <p>Hello World!</p>',
            '    <!--[if lt IE 9]>',
            '    <script src="ieonly.js"></script>',
            '    <![endif]-->',
            '    <select name="foo">',
            '        <option value="1">A</option>',
            '        <option value="2" selected>B</option>',
            '        <option value="3">C</option>',
            '    </select>',
            '</div>',
        ));
        $t = t("div")
            ->append(c("TEST"))
            ->append(t("p")->append("Hello World!"))
            ->append(cond("lt IE 9", t("script")->attr("src", "ieonly.js")))
            ->append(
                s(2, array("A" => 1, "B" => 2, "C" => 3), array("name" => "foo"))
            );
        $this->assertSame($ex1, $t->write());
    }
    
    /**
     * alias() の引数で既に存在する関数名を指定した場合,
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @expectedException \InvalidArgumentException
     * @covers Peach\Markup\Html::alias
     * @covers Peach\Markup\Html::handleAlias
     */
    public function testAliasByAlreadyDefinedName()
    {
        $this->assertFalse(function_exists("func1"));
        Html::alias(array("tag" => "func1"));
        $this->assertTrue(function_exists("func1"));
        Html::alias(array("comment" => "func1"));
    }
    
    /**
     * 同じ引数 (クラスメソッドと関数名の組み合わせ) で alias() を実行した場合,
     * InvalidArgumentException がスローされずに正常終了することを確認します.
     * 
     * @covers Peach\Markup\Html::alias
     * @covers Peach\Markup\Html::handleAlias
     */
    public function testAliasBySameArguments()
    {
        $this->assertFalse(function_exists("func2"));
        $this->assertFalse(function_exists("func3"));
        Html::alias(array("tag" => "func2", "comment" => "func3"));
        $this->assertTrue(function_exists("func2"));
        $this->assertTrue(function_exists("func3"));
        Html::alias(array("tag" => "func2"));
        Html::alias(array("comment" => "func3", "tag" => "func2"));
    }
    
    /**
     * alias() の引数に関数名として使用できない名前を指定した場合,
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Markup\Html::alias
     * @covers Peach\Markup\Html::handleAlias
     */
    public function testAliasByInvalidName()
    {
        $invalidList = array(
            "123invalid",
            "hogehoge()",
            "bad-function",
        );
        foreach ($invalidList as $name) {
            try {
                Html::alias(array("tag" => $name));
                $this->fail();
            } catch (\InvalidArgumentException $e) {}
        }
    }
    
    /**
     * alias() の引数に存在しないメソッド名を指定した場合,
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @expectedException \InvalidArgumentException
     * @covers Peach\Markup\Html::alias
     * @covers Peach\Markup\Html::handleAlias
     */
    public function testAliasByUndefinedMethod()
    {
        Html::alias(array("tag" => "valid1", "hogehoge" => "invalid1"));
    }
    
    /**
     * closure() のテストです. 以下について確認します.
     * 
     * - 返り値のクロージャを実行すると, 指定したメソッドを呼び出すこと
     * - 同じ引数で複数回実行すると, 同一のオブジェクトを返すこと
     * 
     * @covers Peach\Markup\Html::closure
     * @covers Peach\Markup\Html::createClosure
     */
    public function testClosure()
    {
        $t  = Html::closure("tag");
        $co = Html::closure("comment");
        $cc = Html::closure("conditionalComment");
        $s  = Html::closure("select");
        $this->assertInstanceOf("Closure", $t);
        
        $ex1 = implode("\r\n", array(
            '<div>',
            '    <!--TEST-->',
            '    <p>Hello World!</p>',
            '    <!--[if lt IE 9]>',
            '    <script src="ieonly.js"></script>',
            '    <![endif]-->',
            '    <select name="foo">',
            '        <option value="1">A</option>',
            '        <option value="2" selected>B</option>',
            '        <option value="3">C</option>',
            '    </select>',
            '</div>',
        ));
        $result = $t("div")
            ->append($co("TEST"))
            ->append($t("p")->append("Hello World!"))
            ->append($cc("lt IE 9", $t("script")->attr("src", "ieonly.js")))
            ->append(
                $s(2, array("A" => 1, "B" => 2, "C" => 3), array("name" => "foo"))
            );
        $this->assertSame($ex1, $result->write());
        
        $t2 = Html::closure("tag");
        $this->assertSame($t2, $t);
    }
    
    /**
     * closure() の引数に存在しないメソッド名を指定した場合,
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @expectedException \InvalidArgumentException
     * @covers Peach\Markup\Html::closure
     * @covers Peach\Markup\Html::createClosure
     */
    public function testClosureByUndefinedMethod()
    {
        Html::closure("notfound");
    }
    
    /**
     * 引数に指定したそれぞれのメソッド名について, 対応するクロージャを返すことを確認します.
     * 
     * @covers Peach\Markup\Html::closures
     * @covers Peach\Markup\Html::createClosure
     */
    public function testClosures()
    {
        $ex1 = Html::closure("tag");
        $ex2 = Html::closure("comment");
        $ex3 = Html::closure("select");
        $ex4 = Html::closure("conditionalComment");
        
        $c = Html::closures(array("tag", "comment", "select", "conditionalComment"));
        $this->assertSame($ex1, $c["tag"]);
        $this->assertSame($ex2, $c["comment"]);
        $this->assertSame($ex3, $c["select"]);
        $this->assertSame($ex4, $c["conditionalComment"]);
        
        list($c1, $c2, $c3, $c4) = Html::closures(array("tag", "comment", "select", "conditionalComment"));
        $this->assertSame($ex1, $c1);
        $this->assertSame($ex2, $c2);
        $this->assertSame($ex3, $c3);
        $this->assertSame($ex4, $c4);
    }
    
    /**
     * 引数の配列内に存在しないメソッド名が含まれている場合,
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @expectedException \InvalidArgumentException
     * @covers Peach\Markup\Html::closures
     */
    public function testClosuresFail()
    {
        Html::closures(array("tag", "invalid", "comment"));
    }
}
