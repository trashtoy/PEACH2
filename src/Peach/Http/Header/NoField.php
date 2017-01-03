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

use Peach\Http\HeaderField;

/**
 * 存在しない HeaderField をあらわします. (Null Object パターン)
 * 
 * この HeaderField の getName() および format() は空文字列を返します..
 */
class NoField implements HeaderField
{
    /**
     * このクラスを直接インスタンス化することはできません.
     */
    private function __construct() {}
    
    /**
     * 空文字列を返します.
     * 
     * @return string 空文字列
     */
    public function format()
    {
        return "";
    }
    
    /**
     * 空文字列を返します.
     * 
     * @return string 空文字列
     */
    public function getName()
    {
        return "";
    }
    
    /**
     * このフィールドは値を持たないため null を返します.
     * @return null
     */
    public function getValue()
    {
        return null;
    }
    
    /**
     * このクラスの唯一のインスタンスを返します.
     * @return NoField 唯一の NoField インスタンス
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

}
