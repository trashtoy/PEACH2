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
 * @since  2.0.0
 */
namespace Peach\Markup;

/**
 * ノードの生成を簡略化するための API を備えたヘルパーです.
 * このインタフェースを使用することで,
 * ノードの構築およびマークアップ出力の手間を省力化することができます.
 */
interface Helper
{
    /**
     * 新しい HelperObject を生成します.
     * このメソッドが返す HelperObject は, 引数の型に応じて以下のように振る舞います.
     * 
     * - 文字列の場合: その文字列を要素名に持つ {@link Element}
     * - null または空文字列の場合: 空の {@link NodeList}
     * - Node オブジェクトの場合: その Node 自身
     * 
     * 第 2 引数に配列を指定した場合, 生成された要素に対して属性をセットすることが出来ます.
     * (生成された HelperObject が要素ではない場合, 第 2 引数は無視されます)
     * 
     * @param  string|Component $var
     * @param  array $attr
     * @return HelperObject
     */
    public function tag($var, $attr = array());
    
    /**
     * 指定された HelperObject を別の形式 (例えば HTML コードなど) に変換します.
     * 
     * @param  HelperObject $object 変換対象の HelperObject
     * @return mixed                変換結果
     */
    public function write(HelperObject $object);
    
    /**
     * 指定された要素名を持つ新しい Element オブジェクトを返します.
     * 
     * @param  string  $name 要素名
     * @return Element       指定された要素名を持つ Element
     */
    public function createElement($name);
}
