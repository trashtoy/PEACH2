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

use Peach\Http\HeaderItem;

class Status implements HeaderItem
{
    /**
     * 3 桁の数字から成る文字列です.
     * 
     * @var string
     */
    private $statusCode;
    
    /**
     * HTTP ステータスの "Reason-phrase" をあらわす文字列です.
     * 
     * @var string
     */
    private $reasonPhrase;
    
    /**
     * 
     * @param string $code         "200", "404" など, 3 桁の数字から成る文字列
     * @param string $reasonPhrase "OK", "Not Found" など, HTTP ステータスの Reason-Phrase に相当する文字列 (省略可能)
     */
    public function __construct($code, $reasonPhrase = "")
    {
        $this->code         = $code;
        $this->reasonPhrase = $reasonPhrase;
    }
    
    /**
     * 3 桁のステータスコードを返します.
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * このステータスの ReasonPhrase 部分を返します.
     * 
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
    
    /**
     * このメソッドは HTTP/2 ベースのレスポンスでのみ利用されます.
     * ステータスコードのみ返します.
     */
    public function format()
    {
        return $this->code;
    }
    
    /**
     * 文字列 ":status" を返します.
     * 
     * @return string このヘッダーの名前 (":status")
     */
    public function getName()
    {
        return ":status";
    }
}
