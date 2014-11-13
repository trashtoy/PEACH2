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
use Peach\Util\Strings;

/**
 * 与えられたノードを HTML や XML などの文字列に変換するクラスです.
 */
class DefaultContext extends Context
{
    /**
     * @var Indent
     */
    private $indent;
    
    /**
     * @var Renderer
     */
    private $renderer;
    
    /**
     * @var BreakControl
     */
    private $breakControl;
    
    /**
     * TRUE の場合はノードの前後にインデントと改行を付加します.
     * @var bool
     */
    private $isIndentMode;
    
    /**
     * TRUE の場合はコメントの内部を処理している状態とみなします.
     * コメントの内部にあるコメントは無視するようになります.
     * @var bool
     */
    private $isCommentMode;
    
    /**
     * handle() メソッド実行時の処理結果が格納されます.
     * @var string
     */
    private $result;
    
    /**
     * 指定された Renderer, Indent, BreakControl オブジェクトを使って
     * マークアップを行う DefaultContext オブジェクトを構築します.
     * 
     * @param Renderer     $renderer
     * @param Indent       $indent
     * @param BreakControl $breakControl
     */
    public function __construct(Renderer $renderer, Indent $indent = null, BreakControl $breakControl = null)
    {
        if (!isset($indent)) {
            $indent = new Indent();
        }
        if (!isset($breakControl)) {
            $breakControl = DefaultBreakControl::getInstance();
        }
        $this->renderer      = $renderer;
        $this->indent        = $indent;
        $this->breakControl  = $breakControl;
        $this->isIndentMode  = true;
        $this->isCommentMode = false;
        $this->result        = "";
    }
    
    /**
     * コメントノードを読み込みます.
     * @param Comment
     */
    public function handleComment(Comment $comment)
    {
        if ($this->isCommentMode) {
            $this->formatChildNodes($comment);
            return;
        }
        
        $this->isCommentMode = true;
        $prefix = $this->escapeEndComment($comment->getPrefix());
        $suffix = $this->escapeEndComment($comment->getSuffix());
        $this->result .= $this->indent() . "<!--{$prefix}";
        if ($this->isIndentMode) {
            if ($this->checkBreakModeInComment($comment)) {
                $breakCode = $this->breakCode();
                $this->result .= $breakCode;
                $this->formatChildNodes($comment);
                $this->result .= $breakCode;
                $this->result .= $this->indent();
            } else {
                $this->isIndentMode = false;
                $this->formatChildNodes($comment);
                $this->isIndentMode = true;
            }
        } else {
            $this->formatChildNodes($comment);
        }
        $this->result .= "{$suffix}-->";
        $this->isCommentMode = false;
    }
    
    private function checkBreakModeInComment(Comment $comment)
    {
        $nodes = $comment->getChildNodes();
        switch (count($nodes)) {
            case 0:
                return false;
            case 1:
                $node = $nodes[0];
                if ($node instanceof Comment) {
                    return $this->checkBreakModeInComment($node->getChildNodes());
                }
                
                return ($node instanceof Element);
            default:
                return true;
        }
    }
    
    /**
     * Text ノードを読み込みます.
     * @param Text $text
     */
    public function handleText(Text $text) {
        $this->result .= $this->indent() . $this->escape($text->getText());
    }
    
    /**
     * Code を読み込みます.
     * @param Code $code
     */
    public function handleCode(Code $code)
    {
        $text   = $code->getText();
        if (!strlen($text)) {
            return;
        }
        
        $lines  = Strings::getLines($text);
        $indent = $this->indent();
        $this->result .= $indent;
        $this->result .= implode($this->breakCode() . $indent, $lines);
    }
    
    /**
     * EmptyElement を読み込みます.
     * @param EmptyElement
     * @see Context::handleEmptyElement()
     */
    public function handleEmptyElement(EmptyElement $node) {
        $this->result .= $this->indent() . $this->renderer->formatEmptyTag($node);
    }
    
    /**
     * ContainerElement を読み込みます.
     * @param ContainerElement
     * @see Context::handleContainerElement()
     */
    public function handleContainerElement(ContainerElement $element)
    {
        $this->result .= $this->indent() . $this->renderer->formatStartTag($element);
        if ($this->isIndentMode) {
            if ($this->breakControl->breaks($element)) {
                $this->result .= $this->indent->stepUp();
                $this->result .= $this->formatChildNodes($element);
                $this->result .= $this->breakCode();
                $this->result .= $this->indent->stepDown();
            } else {
                $this->isIndentMode = false;
                $this->formatChildNodes($element);
                $this->isIndentMode = true;
            }
        } else {
           $this->formatChildNodes($element);
        }
        $this->result .= $this->renderer->formatEndTag($element);
    }
    
    /**
     * NodeList を変換します.
     * @param NodeList $node
     */
    public function handleNodeList(NodeList $node)
    {
        $this->formatChildNodes($node);
    }
    
    /**
     * マークアップされたコードを返します.
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * 指定されたコンテナの子ノードを書式化します.
     * 各子ノードの出力結果の末尾には, 改行コードで連結されます. (インデントモードが ON の場合)
     * 末尾の子ノードの出力結果の後ろに改行コードは付きません.
     * 
     * @param Container $container
     */
    private function formatChildNodes(Container $container)
    {
        $nextBreak  = "";
        $breakCode  = $this->breakCode();
        $childNodes = $container->getChildNodes();
        foreach ($childNodes as $child) {
            $this->result .= $nextBreak;
            $this->handle($child);
            $nextBreak = $breakCode;
        }
    }
    
    /**
     * None を処理します. 何もせずに終了します.
     * 
     * @param None $none
     */
    public function handleNone(None $none)
    {
    }
    
    /**
     * @return string
     */
    private function indent()
    {
        return $this->isIndentMode ? $this->indent->indent() : "";
    }
    
    /**
     * 
     * @return string
     */
    private function breakCode()
    {
        return $this->isIndentMode ? $this->indent->breakCode() : "";
    }
    
    /**
     * @param  string $text
     * @return string
     */
    private function escape($text)
    {
        return preg_replace("/\\r\\n|\\r|\\n/", "&#xa;", htmlspecialchars($text));
    }
    
    /**
     * @param  string $text
     * @return string
     */
    private function escapeEndComment($text)
    {
        return str_replace("-->", "--&gt;", $text);
    }
}
