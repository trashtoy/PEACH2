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
 * @since  2.2.0
 */
namespace Peach\Markup;

/**
 * HTML や RSS など, 特定のマークアップ言語に特化した Helper
 * クラスを新たに実装する際に利用するスケルトン実装です.
 * 
 * このクラスおよびその具象クラスは Decorator パターンで実装されています.
 */
abstract class AbstractHelper implements Helper
{
    /**
     * このオブジェクトに設定されているベースの Helper オブジェクトです.
     * Helper インタフェースで定義されている各種メソッドの実際の処理をこのメンバ変数に移譲しています.
     * 
     * @var Helper
     */
    private $parent;
    
    /**
     * 指定された Helper オブジェクトをベースとして, 新しいインスタンスを生成します.
     * 
     * @param Helper $parent ベースの Helper オブジェクト
     */
    public function __construct(Helper $parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * このオブジェクトに設定されているベースの Helper オブジェクトを返します.
     * 
     * @return Helper ベースの Helper オブジェクト
     */
    public function getParentHelper()
    {
        return $this->parent;
    }
    
    /**
     * ベースの Helper オブジェクトの createElement() を実行します.
     * 
     * @param  string  $name 要素名
     * @return Element       指定された要素名を持つ Element
     */
    public function createElement($name)
    {
        return $this->parent->createElement($name);
    }
    
    /**
     * ベースの Helper オブジェクトの tag() メソッドの結果をそのまま返します.
     * 
     * @param  string|Component $var
     * @param  array $attr
     * @return HelperObject
     */
    public function tag($var, $attr = array())
    {
        return $this->parent->tag($var, $attr);
    }
    
    /**
     * ベースの Helper オブジェクトの write() メソッドの結果をそのまま返します.
     * 
     * @param  HelperObject $object
     * @return mixed
     */
    public function write(HelperObject $object)
    {
        return $this->parent->write($object);
    }
}
