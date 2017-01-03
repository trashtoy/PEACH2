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
 * 汎用的な Helper オブジェクトです.
 * コンストラクタ引数に指定された Builder および空要素タグ一覧をもとにノードの生成・出力を行います.
 * 
 * このクラスのインスタンスは, AbstractHelper の各派生クラスにおいて親の
 * Helper オブジェクトとして使用されることを想定しています.
 */
class BaseHelper implements Helper
{
    /**
     * 出力の際に使用される Builder です
     * @var Builder
     */
    private $builder;
    
    /**
     * 空要素として扱われる要素名の一覧です
     * @var array
     */
    private $emptyNodeNames;
    
    /**
     * 指定された Builder を使ってマークアップを行う, 新しい Helper インスタンスを生成します.
     * 第二引数で, 空要素として扱われる要素名の一覧を指定することができます.
     * @param Builder $builder        マークアップに利用する Builder
     * @param array   $emptyNodeNames 空要素の要素名一覧
     */
    public function __construct(Builder $builder, array $emptyNodeNames = array())
    {
        $this->builder        = $builder;
        $this->emptyNodeNames = $emptyNodeNames;
    }
    
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
    public function tag($var, $attr = array())
    {
        $object = new HelperObject($this, $var);
        if (count($attr)) {
            $object->attr($attr);
        }
        return $object;
    }
    
    /**
     * 指定された HelperObject の変換結果を返します.
     * このヘルパーに設定されている Builder を使って, 引数の HelperObject を build した結果を返します.
     * @param  HelperObject $object
     * @return mixed
     */
    public function write(HelperObject $object)
    {
        return $this->builder->build($object->getNode());
    }
    
    /**
     * この Helper にセットされている Builder オブジェクトを返します.
     * 返り値の Builder に対する変更は, この Helper にも影響されます.
     * 
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
    
    /**
     * この Helper にセットされている Builder を,
     * 引数の Builder オブジェクトで上書きします.
     * 
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }
    
    /**
     * 指定された要素名を持つ新しい Element オブジェクトを返します.
     * 
     * @param  string  $name 要素名
     * @return Element       指定された要素名を持つ Element
     */
    public function createElement($name)
    {
        return in_array($name, $this->emptyNodeNames) ?
            new EmptyElement($name) : new ContainerElement($name);
    }
}
