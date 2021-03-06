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
 * {@link Builder} で処理可能なパーツであることを示すインタフェースです.
 * このインタフェースを実装したオブジェクトは
 * {@link Builder::build()} メソッドの引数に指定して
 * HTML コードなどに加工することが出来ます.
 */
interface Component
{
    /**
     * 指定された Context にこのオブジェクトを処理させます. Visitor パターンの accept() に相当します.
     * @param Context $context この Component を処理する Context
     */
    public function accept(Context $context);
    
    /**
     * このオブジェクトが {@link Container::appendNode()} の引数に指定された際に,
     * 実際に追加されるノードの一覧を返します.
     * 
     * @return NodeList Container に追加されるオブジェクト
     */
    public function getAppendee();
}
