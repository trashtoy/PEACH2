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
use Peach\DF\JsonCodec\Expression;
use Peach\DF\JsonCodec;

/**
 * JSON の BNF ルール number をあらわす Expression です.
 * RFC 7159 で定義されている以下のフォーマットを解釈します.
 * 
 * <pre>
 * number = [ minus ] int [ frac ] [ exp ]
 * 
 * decimal-point = %x2E       ; .
 * 
 * digit1-9 = %x31-39         ; 1-9
 * 
 * e = %x65 / %x45            ; e E
 * 
 * exp = e [ minus / plus ] 1*DIGIT
 * 
 * frac = decimal-point 1*DIGIT
 * 
 * int = zero / ( digit1-9 *DIGIT )
 * 
 * minus = %x2D               ; -
 * 
 * plus = %x2B                ; +
 * 
 * zero = %x30                ; 0
 * </pre>
 * 
 * @ignore
 */
class Number implements Expression
{
    /**
     * handle() の解析結果です.
     * 
     * @var string
     */
    private $result;
    
    /**
     * handle() の解析結果が float かどうかを判定します.
     * 
     * @var bool
     */
    private $isFloat;
    
    /**
     * PHP の int 型変数で表現できない巨大整数について, 
     * string 型に変換するかどうかを表すオプションです.
     * 
     * @var bool
     */
    private $bigNumAsString;
    
    /**
     * 新しい Number インスタンスを構築します.
     */
    public function __construct()
    {
        $this->result         = "";
        $this->isFloat        = false;
        $this->bigNumAsString = false;
    }
    
    /**
     * number = [ minus ] int [ frac ] [ exp ]
     * 
     * @param \Peach\DF\JsonCodec\Context $context
     */
    public function handle(Context $context)
    {
        $this->bigNumAsString = $context->getOption(JsonCodec::BIGINT_AS_STRING);
        $this->handleMinus($context);
        $this->handleIntegralPart($context);
        $this->handleFractionPart($context);
        $this->handleExponentPart($context);
    }
    
    /**
     * "-" 記号を読み込みます.
     * 
     * @param Context $context
     */
    private function handleMinus(Context $context)
    {
        if ($context->current() === "-") {
            $this->result .= "-";
            $context->next();
        }
    }
    
    /**
     * 数値部分 (以下の BNF 記法) を読み込みます.
     * 
     * int = zero / ( digit1-9 *DIGIT )
     * 
     * @param Context $context
     */
    private function handleIntegralPart(Context $context)
    {
        // check zero
        if ($context->current() === "0") {
            if (preg_match("/^0[0-9]$/", $context->getSequence(2))) {
                throw $context->createException("Integral part must not start with 0");
            }
            $this->result .= "0";
            $context->next();
            return;
        }
        
        // check ( digit1-9 *DIGIT )
        $this->handleFirstDigit($context);
        $this->handleDigitSequence($context);
    }
    
    /**
     * 1 文字目の数字を読み込みます.
     * 
     * @param Context $context
     */
    private function handleFirstDigit(Context $context)
    {
        if (self::checkDigit($context)) {
            $this->result .= $context->current();
            $context->next();
        } else {
            throw $context->createException("Invalid number format");
        }
    }
    
    /**
     * 2 文字目以降の数字列を読み込みます.
     * 
     * @param Context $context
     */
    private function handleDigitSequence(Context $context)
    {
        while ($context->hasNext()) {
            if (self::checkDigit($context)) {
                $this->result .= $context->current();
                $context->next();
            } else {
                break;
            }
        }
    }
    
    /**
     * frac = decimal-point 1*DIGIT
     * 
     * @param Context $context
     */
    private function handleFractionPart(Context $context)
    {
        if ($context->current() !== ".") {
            return;
        }
        
        $this->result .= ".";
        $this->isFloat = true;
        $context->next();
        $this->handleFirstDigit($context);
        $this->handleDigitSequence($context);
    }
    
    /**
     * exp = e [ minus / plus ] 1*DIGIT
     * 
     * @param Context $context
     */
    private function handleExponentPart(Context $context)
    {
        // e = %x65 / %x45
        $current = $context->current();
        if ($current !== "e" && $current !== "E") {
            return;
        }
        
        $this->result .= "e";
        $this->isFloat = true;
        $next = $context->next();
        
        // [ minus / plus ]
        if ($next === "+" || $next === "-") {
            $this->result .= $next;
            $context->next();
        }
        
        // 1*DIGIT
        $this->handleFirstDigit($context);
        $this->handleDigitSequence($context);
    }
    
    /**
     * 現在の文字が数字 ("0" から "9") かどうかを調べます.
     * 
     * @param  Context $context 解析対象の Context
     * @return bool             現在の文字が 0 から 9 のいずれかの場合のみ true
     */
    public static function checkDigit(Context $context)
    {
        $code  = $context->currentCodePoint();
        return (0x30 <= $code && $code <= 0x39);
    }
    
    /**
     * 解析結果を返します.
     * 返り値の型は解析内容に応じて整数, 文字列, float のいずれかとなります.
     * 
     * @return numeric
     */
    public function getResult()
    {
        $num = floatval($this->result);
        if ($this->isFloat) {
            return $num;
        }
        
        $border = pow(2, 32);
        if (-$border <= $num && $num < $border) {
            return intval($num);
        } else {
            return $this->bigNumAsString ? $this->result : $num;
        }
    }
}
