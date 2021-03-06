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
use Peach\Util\Values;

/**
 * ノードの配列をあらわすクラスです.
 * 
 * このクラスは Component を実装しているため,
 * {@link Context::handle()} の引数に渡すことが出来ます.
 * (実際の処理は {@link Context::handleNodeList()} で行われます)
 * 
 * ある要素に対して NodeList を追加した場合, このオブジェクト自体ではなく,
 * このオブジェクトに含まれる各ノードが追加されます.
 * 
 * 例えるならば DOM の NodeList と NodeFragment を兼任するクラスです.
 */
class NodeList implements Container
{
    /**
     * Node の配列です.
     * @var array
     */
    private $nodeList;
    
    /**
     * この NodeList を持つノードです. 存在しない場合は null となります.
     * @var Node
     */
    private $owner;
    
    /**
     * 新しい NodeList を生成します.
     * 引数に値を設定した場合, その値をリストに追加した状態で初期化します.
     * 
     * @param Component|array|string $var 追加するノード
     * @param Node $owner この NodeList を内部に持つ Node オブジェクト (エンドユーザーが直接使用することはありません)
     */
    public function __construct($var = null, Node $owner = null)
    {
        $this->nodeList = array();
        $this->owner    = $owner;
        $this->appendNode($var);
    }
    
    /**
     * この NodeList に実際に追加される値を返します.
     * 
     * @param  mixed $var
     * @return Node[] 追加されるノードの配列
     */
    private function prepareAppendee($var)
    {
        if ($var instanceof Node) {
            return $var;
        }
        if ($var instanceof Component) {
            return $var->getAppendee()->getChildNodes();
        }
        
        if (is_array($var)) {
            $result = array();
            foreach ($var as $i) {
                $appendee = $this->prepareAppendee($i);
                if (is_array($appendee)) {
                    array_splice($result, count($result), 0, $appendee);
                } else {
                    $result[] = $appendee;
                }
            }
            return $result;
        }
        if (!isset($var)) {
            return array();
        }
        
        return array(new Text(Values::stringValue($var)));
    }
    
    /**
     * この NodeList の末尾に引数の値を追加します.
     * 
     * 引数がノードの場合は, 引数をそのまま NodeList の末尾に追加します.
     * 
     * 引数が Container の場合は, その Container に含まれる各ノードを追加します.
     * (Container 自身は追加されません)
     * 
     * 引数が配列の場合は, 配列に含まれる各ノードをこの NodeList に追加します.
     * 
     * 引数がノードではない場合は, 引数の文字列表現をテキストノードとして追加します.
     * 
     * @param Node|Container|array|string $var
     */
    public function appendNode($var)
    {
        $appendee = $this->prepareAppendee($var);
        if (isset($this->owner)) {
            $this->checkOwner($appendee);
        }
        if (is_array($appendee)) {
            $this->nodeList = array_merge($this->nodeList, $appendee);
        } else {
            $this->nodeList[] = $appendee;
        }
    }
    
    /**
     * 指定された Context にこのノードを処理させます.
     * {@link Context::handleNodeList()} を呼び出します.
     * @param Context $context
     */
    public function accept(Context $context)
    {
        $context->handleNodeList($this);
    }
    
    /**
     * この NodeList に子ノードを追加する際に,
     * 親子関係が無限ループしないかどうか検査します.
     * 引数がこの NodeList のオーナーだった場合に InvalidArgumentException をスローします.
     * 引数が配列もしくは {@link Container} だった場合は,
     * その子ノードの一覧について再帰的に検査します.
     * 
     * @param  mixed $var 検査対象
     * @throws \InvalidArgumentException 検査対象にこの NodeList のオーナーが含まれていた場合
     */
    private function checkOwner($var)
    {
        if (is_array($var)) {
            foreach ($var as $i) {
                $this->checkOwner($i);
            }
            return;
        }
        
        if ($var instanceof Container) {
            $this->checkOwner($var->getChildNodes());
        }
        if ($var === $this->owner) {
            throw new \InvalidArgumentException("Tree-loop detected.");
        }
    }
    
    /**
     * この NodeList に含まれるノードの個数を返します.
     * @return int ノードの個数
     */
    public function size()
    {
        return count($this->nodeList);
    }
    
    /**
     * この NodeList に含まれるノードの一覧を配列で返します.
     * @return array
     */
    public function getChildNodes()
    {
        return $this->nodeList;
    }

    /**
     * この NodeList 自身を返します.
     * この NodeList が Container に追加される場合,
     * このオブジェクトの代わりに NodeList に含まれる各ノードが追加されます.
     * 
     * @return Component このオブジェクト
     */
    public function getAppendee()
    {
        return $this;
    }
}
