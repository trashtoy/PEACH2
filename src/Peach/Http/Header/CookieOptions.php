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

use Peach\DT\Timestamp;

/**
 * Set-Cookie ヘッダーの各種属性をあらわすクラスです.
 * このクラスのオブジェクトに対する変更は, 関連するすべての CookieItem オブジェクトに影響を与えます.
 */
class CookieOptions
{
    /**
     * expires 属性の時刻をあらわす Timestamp オブジェクトです.
     * 
     * @var Timestamp
     */
    private $expires;
    
    /**
     * 属性を何も持たない, 新しい CookieOptions オブジェクトを構築します.
     */
    public function __construct()
    {
    }
    
    /**
     * expires 属性の時刻を設定します.
     * 引数に null を設定した場合は expires 属性を削除します.
     * 
     * @param Timestamp $expires Set-Cookie の expires 属性として表現される時刻
     */
    public function setExpires(Timestamp $expires = null)
    {
        $this->expires = $expires;
    }
    
    /**
     * expires 属性の時刻を返します.
     * expires 属性が設定されていない場合は null を返します.
     * 
     * @return Timestamp expires 属性の時刻
     */
    public function getExpires()
    {
        return $this->expires;
    }
}
