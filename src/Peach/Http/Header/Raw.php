<?php
/*
 * Copyright (c) 2015 @trashtoy
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
namespace Peach\Http\Header;

use Peach\Http\SingleHeaderField;
use Peach\Http\Util;

/**
 * 単純なテキストデータをあらわす HeaderField です.
 * 
 * このクラスの format() および getValue() は同じ結果を返します.
 */
class Raw implements SingleHeaderField
{
    /**
     * ヘッダー名をあらわす文字列です.
     *
     * @var string
     */
    private $name;
    
    /**
     * ヘッダー値をあらわす文字列です.
     *
     * @var string
     */
    private $value;
    
    /**
     * 指定されたヘッダー名およびヘッダー値を持つ Raw オブジェクトを構築します.
     * 
     * @param string $name  ヘッダー名
     * @param string $value ヘッダー値
     */
    public function __construct($name, $value)
    {
        Util::validateHeaderName($name);
        $this->name = $name;
        $this->value = $value;
    }
    
    /**
     * このヘッダーの値をそのまま返します.
     * 
     * @return string ヘッダー値
     */
    public function format()
    {
        return $this->value;
    }
    
    /**
     * この HeaderField のヘッダー名を返します.
     * 
     * @return string ヘッダー名
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * この HeaderField のヘッダー値として使用される値を返します.
     * 
     * @return string ヘッダー値として使用される値
     */
    public function getValue()
    {
        return $this->value;
    }
}
