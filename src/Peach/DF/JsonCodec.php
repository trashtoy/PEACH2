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

use InvalidArgumentException;
use Peach\DF\JsonCodec\Context;
use Peach\DF\JsonCodec\DecodeException;
use Peach\DF\JsonCodec\Root;
use Peach\Util\Strings;

/**
 * JSON 形式の文字列を扱う Codec です.
 * このクラスは {@link http://tools.ietf.org/html/rfc7159 RFC 7159}
 * の仕様に基いて JSON の decode (値への変換) と encode (値の JSON への変換)
 * を行います.
 * 
 * RFC 7159 によると JSON 文字列のエンコーディングは UTF-8, UTF-16, UTF-32
 * のいずれかであると定義されていますが, この実装は UTF-8 でエンコーディングされていることを前提とします.
 * UTF-8 以外の文字列を decode した場合はエラーとなります.
 */
class JsonCodec implements Codec
{
    /**
     * 
     * @param string $text
     */
    public function decode($text)
    {
        if (Strings::isWhitespace($text)) {
            return null;
        }
        
        try {
            $root = new Root();
            $root->handle(new Context($text));
            return $root->getResult();
        } catch (DecodeException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param mixed $var
     */
    public function encode($var)
    {
        
    }
}
