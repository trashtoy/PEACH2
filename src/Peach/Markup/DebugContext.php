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
namespace Peach\Markup;

/**
 * デバッグのために使用される Context です.
 * この Context は, handle メソッドを実行した時に自動で echo を行います.
 * 出力内容は, ノードツリーの構造を可視化した文字列となります.
 */
class DebugContext extends Context
{
    /**
     * 出力を整形するために使用される Indent です.
     * @var Indent
     */
    private $indent;
    
    /**
     * 出力結果です.
     * @var string
     */
    private $result;
    
    /**
     * true の場合, handle() を実行すると同時に echo を実行します.
     * @var bool
     */
    private $echoMode;
    
    /**
     * 新しい DebugContext を構築します.
     * @param bool $echoMode 自動で出力を行うかどうかの設定 (デフォルトで true)
     */
    public function __construct($echoMode = true)
    {
        $this->indent   = new Indent();
        $this->result   = "";
        $this->echoMode = (bool) $echoMode;
    }
    
    /**
     * @param string $name
     */
    private function startNode($name)
    {
        $result = $this->indent->indent() . $name . " {" . $this->indent->stepUp();
        if ($this->echoMode) {
            echo $result;
        }
        $this->result .= $result;
    }
    
    /**
     * インデントレベルを下げて "}" を出力します.
     */
    private function endNode()
    {
        $result = $this->indent->stepDown() . "}" . $this->indent->breakCode();
        if ($this->echoMode) {
            echo $result;
        }
        $this->result .= $result;
    }
    
    /**
     * @param string $contents
     */
    private function append($contents)
    {
        $result = $this->indent->indent() . $contents . $this->indent->breakCode();
        if ($this->echoMode) {
            echo $result;
        }
        $this->result .= $result;
    }
    
    /**
     * @param Container $container
     */
    private function handleContainer(Container $container)
    {
        foreach ($container->getChildNodes() as $node) {
            $this->handle($node);
        }
    }
    
    /**
     * デバッグ用の出力データを返します.
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * Comment ノードのデバッグ文字列を出力します.
     * 出力内容は以下の通りです.
     * 
     * <code>
     * Comment {
     *     # 子ノードの出力内容
     * }
     * </code>
     * 
     * @param Comment $node
     */
    public function handleComment(Comment $node)
    {
        $this->startNode("Comment");
        $this->handleContainer($node);
        $this->endNode();
    }
    
    /**
     * ContainerElement ノードのデバッグ文字列を出力します.
     * 出力内容は以下の通りです.
     * 
     * <code>
     * ContainerElement(tagName) {
     *     # 子ノードの出力内容
     * }
     * </code>
     * 
     * @param ContainerElement $node
     */
    public function handleContainerElement(ContainerElement $node)
    {
        $name = $node->getName();
        $this->startNode("ContainerElement({$name})");
        $this->handleContainer($node);
        $this->endNode();
    }
    
    /**
     * EmptyElement ノードのデバッグ文字列を出力します.
     * 出力内容は以下の通りです.
     * 
     * <code>
     * EmptyElement(tagName)
     * </code>
     * 
     * @param EmptyElement $node
     */
    public function handleEmptyElement(EmptyElement $node)
    {
        $name = $node->getName();
        $this->append("EmptyElement({$name})");
    }
    
    /**
     * NodeList のデバッグ文字列を出力します.
     * 出力内容は以下の通りです.
     * 
     * <code>
     * NodeList {
     *     # 子ノードの出力内容
     * }
     * </code>
     * 
     * @param NodeList $node
     */
    public function handleNodeList(NodeList $node)
    {
        $this->startNode("NodeList");
        $this->handleContainer($node);
        $this->endNode();
    }
    
    /**
     * Text ノードのデバッグ文字列を出力します.
     * 出力内容は文字列 "Text" です.
     * @param Text $node
     */
    public function handleText(Text $node)
    {
        $this->append("Text");
    }
    
    /**
     * Code ノードのデバッグ文字列を出力します.
     * 出力内容は文字列 "Code" です.
     * @param Code $node
     */
    public function handleCode(Code $node)
    {
        $this->append("Code");
    }
    
    /**
     * None のデバッグ文字列を出力します.
     * 出力内容は文字列 "None" です.
     * @param None $none
     */
    public function handleNone(None $none)
    {
        $this->append("None");
    }
}
