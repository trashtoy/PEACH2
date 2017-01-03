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

use InvalidArgumentException;
use Peach\Http\SingleHeaderField;
use Peach\Util\Values;

/**
 * HTTP レスポンスのステータスを表すクラスです.
 * 
 * このクラスは HTTP/1 におけるステータスライン, HTTP/2 における :status 擬似ヘッダーを表現します.
 */
class Status implements SingleHeaderField
{
    /**
     * 3 桁の数字から成る文字列です.
     * 
     * @var string
     */
    private $code;
    
    /**
     * HTTP ステータスの "Reason-phrase" をあらわす文字列です.
     * 
     * @var string
     */
    private $reasonPhrase;
    
    /**
     * 指定されたステータスコードおよび Reason-Phrase からなる Status インスタンスを構築します.
     * 
     * @param string $code         "200", "404" など, 3 桁の数字から成る文字列
     * @param string $reasonPhrase "OK", "Not Found" など, HTTP ステータスの Reason-Phrase に相当する文字列 (省略可能)
     */
    public function __construct($code, $reasonPhrase = "")
    {
        $this->code         = $this->cleanCode($code);
        $this->reasonPhrase = $reasonPhrase;
    }
    
    /**
     * 引数を 3 桁の数字文字列に変換します.
     * 
     * @param  mixed  $code ステータスコードをあらわす文字列または整数
     * @return string 3 桁の数字から成る文字列
     * @throws InvalidArgumentException 引数がステータスコードとして妥当ではない場合
     */
    private function cleanCode($code)
    {
        $value = Values::stringValue($code);
        if (!strlen($value)) {
            throw new InvalidArgumentException("Code must not be empty");
        }
        if (!preg_match("/\\A[0-9]{3}\\z/", $value)) {
            throw new InvalidArgumentException("Code must be composed of 3 digits.");
        }
        return $value;
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
     * このステータスの Reason-Phrase 部分を返します.
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
     * 
     * @return string
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
    
    /**
     * この Status オブジェクトの情報を返します.
     * 返り値はステータスコードおよび Reason-Phrase から成る要素数 2 の配列となります.
     * 
     * @return array
     */
    public function getValue()
    {
        return array($this->code, $this->reasonPhrase);
    }
    
    /**
     * "200 OK" をあらわす Status オブジェクトを返します.
     * 
     * @return Status
     */
    public static function getOK()
    {
        // @codeCoverageIgnoreStart
        static $ok = null;
        if ($ok === null) {
            $ok = new self("200", "OK");
        }
        // @codeCoverageIgnoreEnd
        
        return $ok;
    }
}
