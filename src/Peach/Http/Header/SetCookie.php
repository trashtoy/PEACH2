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

/**
 * Set-Cookie ヘッダーを表現するクラスです.
 */
class SetCookie implements MultiHeaderField
{
    /**
     * 各 cookie のキーと, そのキーに相当する CookieItem オブジェクトをマッピングする ArrayMap です.
     * 
     * @var ArrayMap
     */
    private $items;
    
    /**
     * 新しい SetCookie オブジェクトを構築します.
     * 引数に cookie のキー, 値, 属性を指定することで, 初期化と同時に
     * 1 個目の cookie を追加することができます.
     * 引数を省略した場合は cookie を何も持たない状態で初期化されます.
     * 
     * @param string        $name    cookie のキー
     * @param string        $value   cookie の値
     * @param CookieOptions $options 各種属性
     */
    public function __construct($name = null, $value = null, CookieOptions $options = null)
    {
        $this->items = new ArrayMap();
        if ($name !== null) {
            $this->setItem($name, $value, $options);
        }
    }
    
    /**
     * 指定されたキーと値の cookie を持つ新しい Set-Cookie ヘッダーを追加します.
     * 
     * @param string        $name    cookie のキー
     * @param string        $value   cookie の値
     * @param CookieOptions $options 各種属性
     */
    public function setItem($name, $value, CookieOptions $options = null)
    {
        $item = new CookieItem($name, $value, $options);
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
