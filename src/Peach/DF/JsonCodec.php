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
     * 文字列を encode する際に使用する Utf8Codec です.
     * 
     * @var Utf8Codec
     */
    private $utf8Codec;
    
    /**
     * 新しい JsonCodec を構築します.
     */
    public function __construct()
    {
        $this->utf8Codec = new Utf8Codec();
    }
    
    /**
     * 指定された JSON 文字列を値に変換します.
     * 
     * 引数が空白文字列 (または null, false) の場合は null を返します.
     * 
     * @param  string $text 変換対象の JSON 文字列
     * @return mixed        変換結果
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
     * 指定された値を JSON 文字列に変換します.
     * 
     * @param  mixed  $var 変換対象の値
     * @return string      JSON 文字列
     */
    public function encode($var)
    {
        return $this->encodeValue($var);
    }
    
    /**
     * encode の本体の処理です.
     * 
     * @param  mixed  $var 変換対象の値
     * @return string      JSON 文字列
     * @ignore
     */
    public function encodeValue($var)
    {
        if ($var === null) {
            return "null";
        }
        if ($var === true) {
            return "true";
        }
        if ($var === false) {
            return "false";
        }
        if (is_integer($var) || is_float($var)) {
            return strval($var);
        }
        if (is_string($var)) {
            return $this->encodeString($var);
        }
        if (is_array($var)) {
            return $this->checkKeySequence($var) ? $this->encodeArray($var) : $this->encodeObject($var);
        }
    }
    
    /**
     * 配列のキーが 0, 1, 2 ... という具合に 0 から始まる整数の連続になっていた場合のみ true,
     * それ以外は false を返します.
     * 
     * @param  array $arr 変換対象の配列
     * @return bool       配列のキーが整数の連続になっていた場合のみ true
     */
    private function checkKeySequence(array $arr) {
        $i = 0;
        foreach (array_keys($arr) as $key) {
            if ($i !== $key) {
                return false;
            }
            $i++;
        }
        return true;
    }
    
    /**
     * 文字列を JSON 文字列に変換します.
     * 
     * @param  string $str 変換対象の文字列
     * @return string      JSON 文字列
     * @ignore
     */
    public function encodeString($str)
    {
        $self        = $this;
        $callback    = function ($num) use ($self) {
            return $self->encodeCodePoint($num);
        };
        $unicodeList = $this->utf8Codec->decode($str);
        return '"' . implode("", array_map($callback, $unicodeList)) . '"';
    }
    
    /**
     * 指定された Unicode 符号点を JSON 文字に変換します.
     * 
     * @param  int $num Unicode 符号点
     * @return string   指定された Unicode 符号点に対応する文字列
     * @ignore
     */
    public function encodeCodePoint($num)
    {
        // @codeCoverageIgnoreStart
        static $encodeList = array(
            0x22 => "\\\"",
            0x5C => "\\\\",
            0x2F => "\\/",
            0x08 => "\\b",
            0x0C => "\\f",
            0x0A => "\\n",
            0x0D => "\\r",
            0x09 => "\\t",
        );
        // @codeCoverageIgnoreEnd
        
        if (array_key_exists($num, $encodeList)) {
            return $encodeList[$num];
        }
        if (0x20 <= $num && $num < 0x80) {
            return chr($num);
        }
        return "\\u" . str_pad(dechex($num), 4, "0", STR_PAD_LEFT);
    }
    
    /**
     * 指定された配列を JSON の array 表記に変換します.
     * 
     * @param  array  $arr 変換対象
     * @return string      JSON 文字列
     */
    private function encodeArray(array $arr)
    {
        $self     = $this;
        $callback = function ($value) use ($self) {
            return $self->encodeValue($value);
        };
        return "[" . implode(",", array_map($callback, $arr)) . "]";
    }
    
    /**
     * 指定された配列を JSON の object 表記に変換します.
     * 
     * @param  array  $arr 変換対象
     * @return string      JSON 文字列
     */
    private function encodeObject(array $arr)
    {
        $self     = $this;
        $callback = function ($key, $value) use ($self) {
            return $self->encodeString($key) . ":" . $self->encodeValue($value);
        };
        return "{" . implode(",", array_map($callback, array_keys($arr), array_values($arr))) . "}";
    }
}
