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

use Peach\Http\MultiHeaderField;
use Peach\Util\ArrayMap;

class SetCookie implements MultiHeaderField
{
    /**
     *
     * @var ArrayMap
     */
    private $items;
    
    /**
     * 
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->items = new ArrayMap();
        if ($name !== null) {
            $this->setItem($name, $value);
        }
    }
    
    /**
     * 指定された Cookie 名および Cookie 値を持つ新しい Set-Cookie ヘッダーを追加します.
     * 
     * @param string $name  Cookie 名
     * @param string $value Cookie 値
     */
    public function setItem($name, $value)
    {
        $item = new CookieItem($name, $value);
        $this->items->put($name, $item);
    }
    
    /**
     * すべての Set-Cookie ヘッダーの値を配列で返します.
     * 
     * @return array すべての Set-Cookie ヘッダー値の配列
     */
    public function format()
    {
        $result = array();
        foreach ($this->items as $item) {
            $result[] = $item->format();
        }
        return $result;
    }
    
    /**
     * 文字列 "set-cookie" を返します.
     * 
     * @return string ヘッダー名 "set-cookie"
     */
    public function getName()
    {
        return "set-cookie";
    }
    
    /**
     * このオブジェクトにセットされている Set-Cookie ヘッダーの一覧を
     * CookieItem オブジェクトの配列として返します.
     * 
     * @return CookieItem[] CookieItem オブジェクトの配列
     */
    public function getValues()
    {
        return array_values($this->items->asArray());
    }
}
