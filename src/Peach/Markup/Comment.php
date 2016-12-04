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
 * マークアップ言語のコメントをあらわすクラスです.
 * 単なるコメントとしての用途だけでなく, 任意のノードをコメントアウトすることも出来ます.
 */
class Comment implements Container, Node
{
    /**
     * 子ノードの一覧です.
     * @var NodeList
     */
    private $nodeList;
    
    /**
     * コメントの先頭に付与される文字列です.
     * この値がセットされている場合, コメントの先頭は
     * "<!--prefix" のようにレンダリングされます.
     * 
     * @var string
     */
    private $prefix;
    
    /**
     * コメントの末尾に付与される文字列です.
     * この値がセットされている場合, コメントの末尾は
     * "suffix-->" のようにレンダリングされます.
     * 
     * @var string
     */
    private $suffix;
    
    /**
     * 指定された prefix と suffix を持つ Comment オブジェクトを構築します.
     * prefix と suffix は, 主に条件付きコメントの先頭 ("[if IE 6]>" など) と
     * 末尾 ("<![endif]" など) に使用されます.
     * 引数を指定しない場合は通常のコメントノードを生成します.
     * 
     * @param string $prefix コメントの冒頭 ("[if IE 6]>" など)
     * @param string $suffix コメントの末尾 ("<![endif]" など)
     */
    public function __construct($prefix = "", $suffix = "")
    {
        $this->nodeList = new NodeList(null, $this);
        $this->prefix   = Values::stringValue($prefix);
        $this->suffix   = Values::stringValue($suffix);
    }
    
    /**
     * コメントの冒頭の文字列を返します.
     * 
     * @return string コメントの冒頭文字列. 存在しない場合は空文字列
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * コメントの末尾の文字列を返します.
     * 
     * @return string コメントの末尾の文字列. 存在しない場合は空文字列
     */
    public function getSuffix()
    {
        return $this->suffix;
    }
    
    /**
     * 指定された Context にこのノードを処理させます.
     * {@link Context::handleComment()} を呼び出します.
     * 
     * @param Context $context このノードを処理する Context
     */
    public function accept(Context $context)
    {
        $context->handleComment($this);
    }
    
    /**
     * このコメントにテキストまたはノードを追加します.
     * ノードを追加した場合, このコメントノードは引数のノードのコメントアウトとして働きます.
     * @param mixed $var このコメントに追加するテキストまたはノード
     */
    public function appendNode($var)
    {
        $this->nodeList->appendNode($var);
    }
    
    /**
     * このコメントノードに含まれる子ノードの一覧を返します.
     * 
     * @return array 子ノードの一覧
     */
    public function getChildNodes()
    {
        return $this->nodeList->getChildNodes();
    }
    
    /**
     * このオブジェクトを {@link Container::appendNode()} に指定した場合,
     * このオブジェクト自身が追加されます.
     * 
     * @return NodeList このオブジェクトを 1 つだけ含んだ NodeList
     */
    public function getAppendee()
    {
        return new NodeList($this);
    }
}
