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

use Peach\Http\RepeatableHeaderField;

class SetCookie implements RepeatableHeaderField
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var string
     */
    private $value;
    
    /**
     *
     * @var Timestamp
     */
    private $expires;
    
    /**
     *
     * @var int
     */
    private $maxAge;
    
    /**
     *
     * @var string
     */
    private $domain;
    
    /**
     *
     * @var string
     */
    private $path;  
    
    /**
     *
     * @var bool
     */
    private $secure;
    
    /**
     *
     * @var bool
     */
    private $httpOnly;
    
    /**
     * 
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }
    
    /**
     * @todo 実装する
     */
    public function format()
    {
        return "";
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
     * このヘッダーがあらわす値を返します.
     * 返り値に path や domain などの各種オプションは含まれません.
     * 
     * @return string このヘッダーの値
     */
    public function getValue()
    {
        return $this->value;
    }
}
