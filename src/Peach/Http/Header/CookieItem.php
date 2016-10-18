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
namespace Peach\Http\Header;

use Peach\Util\Arrays;

/**
 * 1 行分の Set-Cookie ヘッダーをあらわすクラスです.
 */
class CookieItem
{
    /**
     * Cookie 名をあらわす文字列です.
     * 
     * @var string
     */
    private $name;
    
    /**
     * Cookie 値をあらわす文字列です.
     * 
     * @var string
     */
    private $value;
    
    /**
     *
     * @var CookieOptions
     */
    private $options;
    
    /**
     * 指定されたキーおよび値の cookie を持つ CookieItem オブジェクトを構築します.
     * 
     * @param string $name           cookie のキー
     * @param string $value          cookie の値
     * @param CookieOptions $options その cookie が持つ属性
     */
    public function __construct($name, $value, CookieOptions $options = null)
    {
        $this->name    = $name;
        $this->value   = $value;
        $this->options = $options;
    }
    
    /**
     * Set-Cookie ヘッダーの値 ("Set-Cookie: " に続く文字列部分) を書式化します.
     * 
     * @return string Set-Cookie ヘッダーの値部分
     */
    public function format()
    {
        $data = $this->formatData();
        return ($this->options === null) ? $data : implode("; ", Arrays::concat($data, $this->options->formatOptions()));
    }
    
    /**
     * 出力の先頭の "key=value" 部分を書式化します.
     * 
     * @return string "key=value" 形式の文字列
     */
    private function formatData()
    {
        $name  = rawurlencode($this->name);
        $value = rawurlencode($this->value);
        return "{$name}={$value}";
    }
}
