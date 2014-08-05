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
 * 既存の BreakControl の振る舞いを拡張するためのラッパークラスです.
 * このクラスは Decorator パターンで設計されています.
 * このクラスを継承して新しい BreakControl クラスを作り,
 * breaks() をオーバーライドして利用してください.
 */
class BreakControlWrapper implements BreakControl
{
    /**
     *
     * @var BreakControl
     */
    private $original;
    
    /**
     * 指定された BreakControl をラップする BreakControlWrapper を構築します.
     * 引数を省略した場合は DefaultBreakControl をラップします.
     * 
     * @param BreakControl $original 未指定の場合は DefaultBreakControl
     */
    public function __construct(BreakControl $original = null)
    {
        if (!isset($original)) {
            $original = DefaultBreakControl::getInstance();
        }
        $this->original = $original;
    }
    
    /**
     * ラップ対象の BreakControl オブジェクトを返します.
     * @return BreakControl
     */
    public function getOriginal()
    {
        return $this->original;
    }
    
    /**
     * オリジナルの breaks() を呼び出します.
     * 
     * @param  ContainerElement $node
     * @return bool
     */
    public function breaks(ContainerElement $node)
    {
        return $this->original->breaks($node);
    }
}
