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
 * ノードを内部に含めることが出来るインタフェースです.
 * このインタフェースを実装したクラスに
 * {@link NodeList} や {@link ContainerElement} などがあります.
 */
interface Container extends Component
{
    /**
     * このコンテナにノードを追加します.
     * 
     * このメソッドは, 引数の種類によって以下の挙動を取ります.
     * 
     * - {@link Node} の場合, 引数をそのままこの Container に追加します.
     * - {@link Container} でかつ Node ではない場合, 引数の Container に含まれるノードを追加します. (引数の Container 自身は追加されません)
     * - 配列の場合, 配列に含まれる各ノードをこの Container に追加します.
     * - 以上の条件に合致しない場合は, 引数の文字列表現を {@link Text} ノードに変換し, この Container に追加します.
     * 
     * @param Node|Container|array|string $var
     */
    public function appendNode($var);
    
    /**
     * このコンテナの子ノードの一覧を
     * {@link Node} オブジェクトの配列として返します.
     * 
     * @return array
     */
    public function getChildNodes();
}
