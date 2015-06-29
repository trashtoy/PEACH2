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
 * @since  2.0.2
 */
namespace Peach\DF;

/**
 * MIME base64 方式の文字列のエンコードおよびデコードを行うクラスです.
 * 
 * このクラスは
 * {@link http://php.net/manual/ja/function.base64-encode.php base64_encode} および
 * {@link http://php.net/manual/ja/function.base64-decode.php base64_decode}
 * のラッパーとして機能します.
 */
class Base64Codec implements Codec
{
    /**
     * 指定された base64 形式の文字列をデコードします.
     * 
     * @param  string $text base64 でエンコードされた文字列
     * @return string       変換結果
     */
    public function decode($text)
    {
        return base64_decode($text);
    }
    
    /**
     * 指定された文字列を base64 でエンコードします.
     * 
     * @param  string $var エンコード対象の文字列
     * @return string      base64 形式の文字列
     */
    public function encode($var)
    {
        return base64_encode($var);
    }
}
