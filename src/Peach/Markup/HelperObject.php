<?php
/*
 * Copyright (c) 2014 @trashtoy
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
 * @since  2.0.0
 */
namespace Peach\Markup;
use Peach\Util\ArrayMap;
use Peach\Util\Values;

/**
 * 既存の Component をラップして, ノードツリーの構築を簡略化・省力化するための糖衣構文を備えたクラスです.
 * 主に (MVC フレームワークで言うところの) View の範囲で使用されることを想定しています.
 */
class HelperObject implements Container
{
    /**
     * このオブジェクトを生成した Helper オブジェクトです.
     * このオブジェクトの prototype を生成する場合などに使用されます.
     * @var Helper
     */
    private $helper;
    
    /**
     * このオブジェクトがラップしている Component です.
     * @var Component
     */
    private $node;
    
    /**
     * 指定された Helper オブジェクトに紐付けられた新しいインスタンスを構築します.
     * このコンストラクタは {@link Helper::tag()} から呼び出されます.
     * 通常は, エンドユーザーがコンストラクタを直接呼び出す機会はありません.
     * 
     * @param Helper $helper
     * @param mixed $var このオブジェクトがラップする値 (テキスト, Component など)
     */
    public function __construct(Helper $helper, $var)
    {
        $this->helper = $helper;
        $this->node   = $this->createNode($var, $helper);
    }
    
    /**
     * 引数の値をノードに変換します.
     * 返り値は, 引数によって以下のようになります.
     * 
     * - {@link Node} 型オブジェクトの場合: 引数自身
     * - {@link NodeList} 型オブジェクトの場合: 引数自身
     * - {@link HelperObject} 型オブジェクトの場合: 引数のオブジェクトがラップしているノード
     * - 文字列の場合: 引数の文字列を要素名に持つ新しい {@link Element}
     * - null または空文字列の場合: 空の {@link NodeList}
     * - 上記に当てはまらない場合: 引数の文字列表現をあらわす {@link Text} ノード
     * 
     * @param  mixed $var     変換対象の値
     * @param  Helper $helper ノードの生成に利用する Helper オブジェクト
     * @return Component      変換後のノード
     */
    private function createNode($var, Helper $helper)
    {
        if ($var instanceof Node) {
            return $var;
        }
        if ($var instanceof NodeList) {
            return $var;
        }
        if ($var instanceof HelperObject) {
            return $var->getNode();
        }
        if (is_string($var) && strlen($var)) {
            return $helper->createElement($var);
        }
        $nodeName = Values::stringValue($var);
        return strlen($nodeName) ? new Text($nodeName) : new NodeList();
    }
    
    /**
     * このオブジェクトがラップしているノードを返します.
     * @return Component
     */
    public function getNode()
    {
        return $this->node;
    }
    
    /**
     * このオブジェクトの子ノードとして, 指定された値を追加します.
     * このオブジェクトがラップしているオブジェクトが Container でない場合は何もしません.
     * 
     * @param  mixed $var   追加される値
     */
    public function appendNode($var)
    {
        $node = $this->node;
        if ($node instanceof Container) {
            $appendee = ($var instanceof HelperObject) ? $var->getNode() : $var;
            $node->appendNode($appendee);
        }
    }
    
    /**
     * このオブジェクトの子ノードとして, 指定された値を追加して, 最後に自分自身を返します.
     * メソッドチェーンを可能にするための appendNode() のシンタックスシュガーです.
     * 
     * @param  mixed $var   追加される値
     * @return HelperObject 自分自身
     */
    public function append($var)
    {
        $this->appendNode($var);
        return $this;
    }
    
    /**
     * 指定された Container にこのオブジェクトを追加します.
     * 以下の 2 つのコードは, どちらも $obj2 の中に $obj1 を追加しています.
     * <code>
     * $obj1->appendTo($obj2);
     * $obj2->append($obj1);
     * </code>
     * {@link HelperObject::append()}
     * との違いは, 返り値が $obj1 になるか $obj2 になるかという点にあります.
     * 
     * @param  Container    $container 追加先の Container
     * @return HelperObject            自分自身
     */
    public function appendTo(Container $container)
    {
        $container->appendNode($this->getNode());
        return $this;
    }
    
    /**
     * 指定された文字列を整形済コードとして追加します.
     * 
     * @param  string|Code $code 追加対象の整形済文字列
     * @return HelperObject      このオブジェクト自身
     */
    public function appendCode($code)
    {
        if (!($code instanceof Code)) {
            return $this->appendCode(new Code($code));
        }
        
        return $this->append($code);
    }
    
    /**
     * {@link Element::setAttribute()}
     * および
     * {@link Element::setAttributes()}
     * の糖衣構文です.
     * 引数が配列の場合は setAttributes() を実行し,
     * 引数が 1 つ以上の文字列の場合は setAttribute() を実行します.
     * もしもこのオブジェクトがラップしているノードが Element ではなかった場合,
     * このメソッドは何も行いません.
     * 
     * jQuery のようなメソッドチェインを実現するため, このオブジェクト自身を返します.
     * 
     * @param  string|array|ArrayMap $var セットする属性
     * @return HelperObject               このオブジェクト自身
     */
    public function attr()
    {
        $node  = $this->node;
        if (!($node instanceof Element)) {
            return $this;
        }
        
        $count = func_num_args();
        if (!$count) {
            return $this;
        }
        
        $args  = func_get_args();
        $first = $args[0];
        if (($first instanceof ArrayMap) || is_array($first)) {
            $node->setAttributes($first);
        } else {
            $second = (1 < $count) ? $args[1] : null;
            $node->setAttribute($first, $second);
        }
        return $this;
    }
    
    /**
     * このオブジェクトの子ノード一覧をあらわす HelperObject を返します.
     * @return HelperObject
     */
    public function children()
    {
        if ($this->node instanceof NodeList) {
            return $this;
        }
        
        $result = $this->helper->tag(null);
        if ($this->node instanceof Container) {
            $result->append($this->node->getChildNodes());
        }
        return $result;
    }
    
    /**
     * この HelperObject をレンダリングします.
     * 
     * @return mixed 出力結果. デフォルトではマークアップされた結果の文字列
     */
    public function write()
    {
        return $this->helper->write($this);
    }
    
    /**
     * この HelperObject をデバッグ出力します.
     * @return string
     */
    public function debug()
    {
        static $debug = null;
        if ($debug === null) {
           $debug = new DebugBuilder();
        }
        return $debug->build($this);
    }
    
    /**
     * この HelperObject がラップしている要素の属性をコピーして, 新しい要素を生成します.
     * もしもラップしているオブジェクトが Element ではなかった場合は
     * 空の NodeList をラップする HelperObject を返します.
     * 
     * @return HelperObject コピーされた要素をラップする HelperObject
     */
    public function prototype()
    {
        return $this->helper->tag($this->createPrototype());
    }
    
    /**
     * このオブジェクトをプロトタイプとして, 新しい HelperObject を生成します.
     * 
     * @return Element
     */
    private function createPrototype()
    {
        $original = $this->node;
        if ($original instanceof ContainerElement) {
            $node = new ContainerElement($original->getName());
            $node->setAttributes($original->getAttributes());
            return $node;
        }
        if ($original instanceof EmptyElement) {
            $node = new EmptyElement($original->getName());
            $node->setAttributes($original->getAttributes());
            return $node;
        }
        
        return null;
    }
    
    /**
     * このオブジェクトがラップしているノードの accept() を呼び出します.
     * @param Context $context
     */
    public function accept(Context $context)
    {
        $this->node->accept($context);
    }
    
    /**
     * このオブジェクトの子ノードの一覧を取得します.
     * もしもこのオブジェクトがラップしているノードが {@link Container Container}
     * だった場合は, そのオブジェクトの子ノードの一覧を返します.
     * それ以外は空の配列を返します.
     * @return array
     */
    public function getChildNodes()
    {
        $node = $this->node;
        return ($node instanceof Container) ? $node->getChildNodes() : array();
    }
    
    /**
     * このオブジェクトがラップしているノードの getApendee() の結果を返します.
     * 
     * @return NodeList
     */
    public function getAppendee()
    {
        return $this->node->getAppendee();
    }
}
