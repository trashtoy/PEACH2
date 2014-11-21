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

/**
 * 内部に子ノードを含めることができる要素です.
 * 子ノードが存在しない場合は "<tag></tag>" のように書式化されます.
 * 
 * HTML の SCRIPT 要素などはこのクラスを使って表現します.
 */
class ContainerElement extends Element implements Container
{
    /**
     * この要素に登録されている子ノードの一覧です.
     * @var NodeList
     */
    private $childNodes;
    
    /**
     * 指定された要素名を持つコンテナ要素を構築します.
     * 
     * @param string $name 要素名
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->childNodes = new NodeList(null, $this);
    }
    
    /**
     * この要素に子ノードを追加します.
     * 
     * @param  mixed $var 追加する値
     * @throws \InvalidArgumentException 指定されたノードの中にこのノードが存在している場合
     */
    public function append($var)
    {
        $this->childNodes->append($var);
    }
    
    /**
     * すべての子ノードを配列で返します.
     * @return array Node の配列
     */
    public function getChildNodes()
    {
        return $this->childNodes->getChildNodes();
    }
    
    /**
     * 指定された Context にこのノードを処理させます.
     * {@link Context::handleContainerElement()} を呼び出します.
     * @param Context $context このノードを処理する Context
     */
    public function accept(Context $context)
    {
        $context->handleContainerElement($this);
    }
    
    /**
     * この要素が持つ子要素の個数を返します.
     * @return int 子要素の個数
     */
    public function size()
    {
        return $this->childNodes->size();
    }
}
