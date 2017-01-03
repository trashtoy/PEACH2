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
 * @since  2.1.0
 * @ignore
 */
namespace Peach\DF\JsonCodec;

use Peach\Util\ArrayMap;
use Peach\DF\Utf8Codec;

/**
 * デコード対象の文字列とデコードオプションを持つクラスです.
 * 
 * @ignore
 */
class Context
{
    /**
     * デコード対象の文字列を Unicode 符号点の配列に分解するための Utf8Codec です.
     * 
     * @var Utf8Codec;
     */
    private $utf8Codec;
    
    /**
     * 出力方法をカスタマイズするためのオプションです.
     * @var ArrayMap
     */
    private $options;
    
    /**
     * decode 対象の Unicode 文字列 (Unicode 符号点の配列) です.
     * @var array
     */
    private $unicodeList;
    
    /**
     * メンバ変数 $text のバイト数です.
     * @var int
     */
    private $count;
    
    /**
     * 現在の index です.
     * @var int
     */
    private $index;
    
    /**
     * 現在の index が参照している文字です.
     * @var string
     */
    private $current;
    
    /**
     * 現在の行番号です. decode に失敗した際にエラーの発生場所を提示するために使用します.
     * @var int 
     */
    private $row;
    
    /**
     * 現在の列番号です. decode に失敗した際にエラーの発生場所を提示するために使用します.
     * @var int 
     */
    private $col;
    
    /**
     * 指定されたデコード対象文字列とデコードオプションを持つ Context インスタンスを構築します.
     * 
     * @param string $text デコード対象文字列
     * @param ArrayMap $options デコードオプション
     */
    public function __construct($text, ArrayMap $options)
    {
        $this->utf8Codec   = new Utf8Codec();
        $this->unicodeList = $this->utf8Codec->decode($text);
        $this->options     = $options;
        $this->count       = count($this->unicodeList);
        $this->index       = 0;
        $this->row         = 1;
        $this->col         = 1;
        $this->current     = $this->computeCurrent();
    }
    
    /**
     * 指定されたオプションが ON かどうかを調べます.
     * 
     * @param  int $code オプション (JsonCodec で定義されている定数)
     * @return bool      指定されたオプションが ON の場合は true, それ以外は false
     */
    public function getOption($code)
    {
        return $this->options->get($code, false);
    }
    
    /**
     * 次の文字が存在するかどうかを判定します.
     * 
     * @return bool
     */
    public function hasNext()
    {
        return ($this->index < $this->count);
    }
    
    /**
     * この Context が現在指し示している 1 文字を計算します.
     * このメソッドは, このオブジェクトの現在位置 (行数・列数) をデバッグ出力する機能の都合により
     *  "\r\n" の文字列を 1 文字としてカウントします.
     * 
     * @return string
     */
    private function computeCurrent()
    {
        if (!$this->hasNext()) {
            return null;
        }
        $index   = $this->index;
        $current = $this->encodeIndex($index);
        if ($current === "\r" && $this->encodeIndex($index + 1) === "\n") {
            return "\r\n";
        }
        return $current;
    }
    
    /**
     * 指定された Unicode 符号点を文字列に変換します.
     * 
     * @param int $point
     * @return string
     */
    public function encodeCodepoint($point)
    {
        return $this->utf8Codec->encode($point);
    }
    
    /**
     * 指定された相対位置の Unicode 符号点を文字列に変換します.
     * @param int $index
     * @return string
     */
    private function encodeIndex($index)
    {
        return ($index < $this->count) ? $this->encodeCodepoint($this->unicodeList[$index]) : null;
    }
    
    /**
     * この Context が現在指し示している文字を返します.
     * 
     * @return string
     */
    public function current()
    {
        return $this->current;
    }
    
    /**
     * この Context の現在位置の Unicode 符号点を返します.
     * 
     * @return int 現在の文字の Unicode 符号点. もしも現在の文字が存在しない場合は null
     */
    public function currentCodePoint()
    {
        return $this->hasNext() ? $this->unicodeList[$this->index] : null;
    }
    
    /**
     * この Context が指し示す位置でデコードに失敗したことをあらわす DecodeException を返します.
     * 
     * @param string $message
     * @return DecodeException
     */
    public function createException($message)
    {
        return new DecodeException("{$message} at line {$this->row}, column {$this->col}");
    }
    
    /**
     * index を 1 進めます. 次の文字を返します.
     * @return string
     */
    public function next()
    {
        static $breakCode = array("\r", "\n", "\r\n");
        if (!$this->hasNext()) {
            throw $this->createException("Cannnot read next");
        }
        $result = $this->current;
        if (in_array($result, $breakCode)) {
            $this->row++;
            $this->col = 1;
        } else {
            $this->col++;
        }
        $this->index += ($result === "\r\n") ? 2 : 1;
        $current = $this->computeCurrent();
        $this->current = $current;
        return $current;
    }
    
    /**
     * この Context の現在位置から, 指定された文字数分の文字列を返します.
     * @param  int $count
     * @return string
     */
    public function getSequence($count)
    {
        $result = $this->current;
        for ($i = 1; $i < $count; $i++) {
            $result .= $this->encodeIndex($this->index + $i);
        }
        return $result;
    }
    
    /**
     * 指定された文字数だけ index を進めます.
     * @param int $count 文字数
     */
    public function skip($count)
    {
        if ($this->count - $this->index < $count) {
            throw $this->createException("Cannot skip {$count} characters");
        }
        
        $this->index += $count;
        $this->current = $this->computeCurrent();
    }
}
