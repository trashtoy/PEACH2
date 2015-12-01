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
use Peach\Http\Util;
use Peach\DT\Timestamp;
use Peach\DT\HttpDateFormat;

/**
 * Last-Modified や If-Modified-Since など, HTTP-date 形式の値を持つヘッダーです.
 */
class HttpDate implements HeaderField
{
    /**
     * ヘッダー名をあらわす文字列です.
     * @var string
     */
    private $name;
    
    /**
     * このヘッダーがあらわす時刻です. GMT ではなくこのシステムのタイムゾーンを基準とします.
     * @var Timestamp
     */
    private $time;
    
    /**
     * このヘッダーの時刻を書式化するための HttpDateFormat です.
     * @var HttpDateFormat
     */
    private $format;
    
    /**
     * 指定されたヘッダー名および時刻を持つ HttpDate オブジェクトを構築します.
     * オプションとして第 3 引数に任意の HttpDateFormat を指定することができます.
     * デフォルトではシステムのタイムゾーンを基準としてヘッダーを書式化しますが,
     * 特定のタイムゾーンを基準にしたい場合に使用してください.
     * 
     * @param string         $name   ヘッダー名
     * @param Timestamp      $time   時刻 (GMT ではなくシステムのタイムゾーンを基準とする)
     * @param HttpDateFormat $format ヘッダー値を書式化するための HttpDateFormat
     */
    public function __construct($name, Timestamp $time, HttpDateFormat $format = null)
    {
        Util::validateHeaderName($name);
        $this->name   = $name;
        $this->time   = $time;
        $this->format = ($format === null) ? HttpDateFormat::getInstance() : $format;
    }
    
    /**
     * このヘッダーの時刻を HTTP-date 形式で書式化します.
     * 
     * @return string HTTP-date 形式の文字列
     */
    public function format()
    {
        return $this->time->format($this->format);
    }
    
    /**
     * このヘッダーの名前を返します.
     * 
     * @return string ヘッダー名
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * このヘッダーが表現する時刻を返します.
     * 
     * @return Timestamp このヘッダーが表現する時刻
     */
    public function getValue()
    {
        return $this->time;
    }
}
