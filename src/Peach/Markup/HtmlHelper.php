<?php
/*
 * Copyright (c) 2016 @trashtoy
 * https://github.com/trashtoy/
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * PHP class file.
 * @auhtor trashtoy
 * @since  2.2.0
 */
namespace Peach\Markup;

use InvalidArgumentException;
use Peach\Util\Values;

/**
 * HTML の出力に特化した Helper です.
 * XML 宣言, DOCTYPE 宣言, select 要素やコメントの出力機能などを備えています.
 * 
 * このクラスは通常コンストラクタではなく newInstance() メソッドを使って初期化を行います.
 * フォーマットのきめ細やかなカスタマイズを行いたい場合のみコンストラクタから生成してください.
 */
class HtmlHelper extends AbstractHelper
{

    /**
     * HTML 4.01 Strict のモードです.
     * 
     * @var string
     */
    const MODE_HTML4_STRICT        = "html4s";
    
    /**
     * HTML 4.01 Transitional のモードです.
     * 
     * @var string
     */
    const MODE_HTML4_TRANSITIONAL  = "html4t";
    
    /**
     * XHTML 1.0 Strict のモードです.
     * 
     * @var string
     */
    const MODE_XHTML1_STRICT       = "xhtml1s";
    
    /**
     * XHTML 1.0 Transitional のモードです.
     * 
     * @var string
     */
    const MODE_XHTML1_TRANSITIONAL = "xhtml1t";
    
    /**
     * XHTML 1.1 のモードです.
     * 
     * @var string
     */
    const MODE_XHTML1_1            = "xhtml1_1";
    
    /**
     * HTML5 のモードです.
     * 
     * @var string
     */
    const MODE_HTML5               = "html5";
    
    /**
     * このオブジェクトが出力する文書型宣言のコードです.
     * 
     * @var string
     */
    private $docType;
    
    /**
     * このオブジェクトが XHTML 形式かどうかをあらわします.
     * xmlDec() メソッドの返り値に影響します.
     * 
     * @var bool
     */
    private $isXhtml;
    
    /**
     * 指定された Helper オブジェクトを利用して HTML タグの出力を行う, 新しい
     * HtmlHelper オブジェクトを構築します.
     * 
     * 第 2 引数の文字列は docType() メソッドの返り値に関係します.
     * もしも未指定の場合, このオブジェクトが docType() メソッドで生成する
     * Component は何も出力しません.
     * 第 3 引数のフラグは xmlDec() メソッドの返り値に関係します.
     * true の場合は xmlDec() が返す Component は XML 宣言を出力しますが,
     * それ以外の場合は何も出力しません.
     * 
     * @param Helper $parent  カスタマイズ対象の Helper オブジェクト
     * @param string $docType この Helper が生成する文書型宣言の文字列
     * @param bool   $isXhtml XHTML として生成する場合のみ true
     */
    public function __construct(Helper $parent, $docType = null, $isXhtml = null)
    {
        parent::__construct($parent);
        $this->docType = $docType;
        $this->isXhtml = $isXhtml;
    }
    
    /**
     * 指定されたモードで HtmlHelper オブジェクトを生成します.
     * 引数には以下の定数を指定してください.
     * 
     * - {@link HtmlHelper::MODE_HTML4_STRICT}
     * - {@link HtmlHelper::MODE_HTML4_TRANSITIONAL}
     * - {@link HtmlHelper::MODE_XHTML1_STRICT}
     * - {@link HtmlHelper::MODE_XHTML1_TRANSITIONAL}
     * - {@link HtmlHelper::MODE_XHTML1_1}
     * - {@link HtmlHelper::MODE_HTML5}
     * 
     * @param  string $mode
     * @return HtmlHelper
     */
    public static function newInstance($mode = null)
    {
        $actualMode     = self::detectMode($mode);
        $baseHelper     = self::createBaseHelper($actualMode);
        $docType        = self::getDocTypeFromMode($actualMode);
        $isXhtml        = self::checkXhtmlFromMode($actualMode);
        return new self($baseHelper, $docType, $isXhtml);
    }
    
    /**
     * 指定されたモードに応じた BaseHelper を返します.
     * 
     * @param  string $mode
     * @return BaseHelper
     */
    private static function createBaseHelper($mode)
    {
        $isXhtml        = self::checkXhtmlFromMode($mode);
        $builder        = self::createBuilder($isXhtml);
        $emptyNodeNames = self::getEmptyNodeNames();
        return new BaseHelper($builder, $emptyNodeNames);
    }
    
    /**
     * XML 宣言をあらわす Component を返します.
     * もしもこの HtmlHelper が XHTML モードで生成された場合,
     * このメソッドは以下のコードを出力する {@link Code} オブジェクトを返します.
     * <code>
     * <?xml version="1.0" encoding="UTF-8"?>
     * </code>
     * 
     * それ以外は {@link None} オブジェクトを返します.
     * 
     * @return Component XML 宣言をあらわす Code オブジェクトまたは None
     */
    public function xmlDec()
    {
        return $this->isXhtml ? new Code('<?xml version="1.0" encoding="UTF-8"?>') : None::getInstance();
    }
    
    /**
     * 文書型宣言をあらわす Code オブジェクトを返します.
     * 返り値の Component が出力するコードは, このオブジェクトの初期化時に指定されたモード
     * (あるいはコンストラクタの第 2 引数) に応じて異なる文書型宣言となります.
     * 
     * @return Component 文書型宣言をあらわす Code オブジェクト. ただし初期化時に指定されていない場合は None
     */
    public function docType()
    {
        return strlen($this->docType) ? new Code($this->docType) : None::getInstance();
    }
    
    /**
     * 指定された内容のコメントノードを作成します.
     * 引数にノードを指定した場合, そのノードの内容をコメントアウトします.
     * 
     * 第 2, 第 3 引数にコメントの接頭辞・接尾辞を含めることが出来ます.
     * 
     * @param  string|Component $contents コメントにしたいテキストまたはノード
     * @param  string $prefix コメントの接頭辞
     * @param  string $suffix コメントの接尾辞
     * @return HelperObject
     */
    public function comment($contents = null, $prefix = "", $suffix = "")
    {
        $comment = new Comment($prefix, $suffix);
        return $this->tag($comment)->append($contents);
    }
    
    /**
     * IE 9 以前の Internet Explorer で採用されている条件付きコメントを生成します.
     * 以下にサンプルを挙げます.
     * <code>
     * echo $htmlHelper->conditionalComment("lt IE 7", "He died on April 9, 2014.")->write();
     * </code>
     * このコードは次の文字列を出力します.
     * <code>
     * <!--[if lt IE 7]>He died on April 9, 2014.<![endif]-->
     * </code>
     * 第 2 引数を省略した場合は空の条件付きコメントを生成します.
     * 
     * @param  string                        $cond     条件文 ("lt IE 7" など)
     * @param  string|Component $contents 条件付きコメントで囲みたいテキストまたはノード
     * @return HelperObject 条件付きコメントを表現する HelperObject
     */
    public function conditionalComment($cond, $contents = null)
    {
        return $this->comment($contents, "[if {$cond}]>", "<![endif]");
    }
    
    /**
     * HTML の select 要素を生成します.
     * 第 1 引数にはデフォルトで選択されている値,
     * 第 2 引数には選択肢を配列で指定します.
     * キーがラベル, 値がそのラベルに割り当てられたデータとなります.
     * 
     * 引数を二次元配列にすることで, 一次元目のキーを optgroup にすることが出来ます.
     * 以下にサンプルを挙げます.
     * <code>
     * $candidates = array(
     *     "Fruit"   => array(
     *         "Apple"  => 1,
     *         "Orange" => 2,
     *         "Pear"   => 3,
     *         "Peach"  => 4,
     *     ),
     *     "Dessert" => array(
     *         "Chocolate" => 5,
     *         "Doughnut"  => 6,
     *         "Ice cream" => 7,
     *     ),
     *     "Others" => 8,
     * );
     * $select = Html::createSelectElement("6", $candidates, array("class" => "sample", "name" => "favorite"));
     * </code>
     * この要素を出力すると以下の結果が得られます.
     * <code>
     * <select class="sample" name="favorite">
     *     <optgroup label="Fruit">
     *         <option value="1">Apple</option>
     *         <option value="2">Orange</option>
     *         <option value="3">Pear</option>
     *         <option value="4">Peach</option>
     *     </optgroup>
     *     <optgroup label="Dessert">
     *         <option value="5">Chocolate</option>
     *         <option value="6" selected>Doughnut</option>
     *         <option value="7">Ice cream</option>
     *     </optgroup>
     *     <option value="8">Others</option>
     * </select>
     * </code>
     * 
     * @param  string $current    デフォルト値
     * @param  array  $candidates 選択肢の一覧
     * @param  array  $attr       追加で指定する属性 (class, id, style など)
     * @return ContainerElement HTML の select 要素
     */
    public function createSelectElement($current, array $candidates, array $attr = array())
    {
        $currentText = Values::stringValue($current);
        $select      = new ContainerElement("select");
        $select->setAttributes($attr);
        $select->appendNode(self::createOptions($currentText, $candidates));
        return $select;
    } 
   
    /**
     * select 要素に含まれる option の一覧を作成します.
     * 
     * @param  string $current    デフォルト値
     * @param  array  $candidates 選択肢の一覧
     * @return NodeList option 要素の一覧
     */
    private function createOptions($current, array $candidates)
    {
        $result = new NodeList();
        foreach ($candidates as $key => $value) {
            if (is_array($value)) {
                $optgroup = new ContainerElement("optgroup");
                $optgroup->setAttribute("label", $key);
                $optgroup->appendNode($this->createOptions($current, $value));
                $result->appendNode($optgroup);
                continue;
            }
            
            $option  = new ContainerElement("option");
            $option->setAttribute("value", $value);
            $value   = Values::stringValue($value);
            if ($current === $value) {
                $option->setAttribute("selected");
            }
            $option->appendNode($key);
            $result->appendNode($option);
        }
        return $result;
    }
    
    /**
     * HTML の select 要素を生成し, 結果を HelperObject として返します.
     * 引数および処理内容は
     * {@link Html::createSelectElement()}
     * と全く同じですが, 生成された要素を HelperObject でラップするところが異なります.
     * 
     * @see    HtmlHelper::createSelectElement
     * @param  string $current    デフォルト値
     * @param  array  $candidates 選択肢の一覧
     * @param  array  $attr       追加で指定する属性 (class, id, style など)
     * @return HelperObject
     */
    public function select($current, array $candidates, array $attr = array())
    {
        return $this->tag(self::createSelectElement($current, $candidates, $attr));
    }
    
    /**
     * newInstance() の引数で指定された文字列のバリデーションを行います.
     * 未対応の文字列が指定された場合は InvalidArgumentException をスローします.
     * 
     * @param  string $param
     * @return string
     * @throws InvalidArgumentException
     */
    private static function detectMode($param)
    {
        static $mapping = null;
        if ($mapping === null) {
            $keys = array(
                self::MODE_HTML4_TRANSITIONAL,
                self::MODE_HTML4_STRICT,
                self::MODE_XHTML1_STRICT,
                self::MODE_XHTML1_TRANSITIONAL,
                self::MODE_XHTML1_1,
                self::MODE_HTML5,
            );
            $altMapping = array(
                ""      => self::MODE_HTML5,
                "html"  => self::MODE_HTML5,
                "html4" => self::MODE_HTML4_TRANSITIONAL,
                "xhtml" => self::MODE_XHTML1_TRANSITIONAL,
            );
            $mapping = array_merge(array_combine($keys, $keys), $altMapping);
        }
        
        $key = strtolower($param);
        if (array_key_exists($key, $mapping)) {
            return $mapping[$key];
        } else {
            throw new InvalidArgumentException("Invalid mode name: {$param}");
        }
    }
    
    /**
     * 指定されたモードが XHTML かどうかを判定します.
     * 
     * @param  string $mode モード (ただし detectMode() でバリデーション済み)
     * @return bool         XHTML と判定された場合のみ true
     */
    private static function checkXhtmlFromMode($mode)
    {
        static $xhtmlModes = array(
            self::MODE_XHTML1_STRICT,
            self::MODE_XHTML1_TRANSITIONAL,
            self::MODE_XHTML1_1,
        );
        return in_array($mode, $xhtmlModes);
    }
    
    /**
     * 指定されたモードに応じた DOCTYPE 宣言の文字列を返します.
     * 
     * @param  string $mode モード (ただし detectMode() でバリデーション済み)
     * @return string       DOCTYPE 宣言
     */
    private static function getDocTypeFromMode($mode)
    {
        static $docTypeList = array(
            self::MODE_HTML4_STRICT        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
            self::MODE_HTML4_TRANSITIONAL  => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
            self::MODE_XHTML1_STRICT       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            self::MODE_XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            self::MODE_XHTML1_1            => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            self::MODE_HTML5               => '<!DOCTYPE html>',
        );
        return $docTypeList[$mode];
    }
    
    /**
     * 新しい DefaultBuilder を生成します.
     * @param  bool $isXhtml XHTML 形式の場合は true, HTML 形式の場合は false
     * @return DefaultBuilder
     */
    private static function createBuilder($isXhtml = false)
    {
        // @codeCoverageIgnoreStart
        static $breakControl = null;
        if (!isset($breakControl)) {
            $breakControl = new NameBreakControl(
                array("html", "head", "body", "ul", "ol", "dl", "table"),
                array("pre", "code", "textarea")
            );
        }
        // @codeCoverageIgnoreEnd
        
        $renderer = $isXhtml ? XmlRenderer::getInstance() : SgmlRenderer::getInstance();
        $builder  = new DefaultBuilder();
        $builder->setBreakControl($breakControl);
        $builder->setRenderer($renderer);
        return $builder;
    }
    
    /**
     * HTML4, XHTML1, HTML5 にて定義されている空要素タグまたは Void elements
     * の一覧を返します.
     * 返り値の配列は, 以下に挙げる要素名をマージしたものとなります.
     * 
     * Empty elements by XHTML1 (https://www.w3.org/TR/xhtml1/)
     *     - area, base, basefont, br, col, frame, hr, img, input, isindex, link, meta, param
     * Void elements by HTML5 (https://www.w3.org/TR/html5/syntax.html#void-elements)
     *     - area, base, br, col, embed, hr, img, input, keygen, link, meta, param, source, track, wbr
     * 
     * @return array
     * @ignore
     */
    private static function getEmptyNodeNames()
    {
        static $emptyList = null;
        if ($emptyList === null) {
            $emptyList = array("area", "base", "basefont", "br", "col", "embed", "frame", "hr", "img", "input", "isindex", "keygen", "link", "meta", "param", "source", "track", "wbr");
        }
        return $emptyList;
    }
}
