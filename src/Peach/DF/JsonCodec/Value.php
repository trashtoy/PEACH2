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
 * @since  2.0.2
 * @ignore
 */
namespace Peach\DF\JsonCodec;

/**
 * JSON の BNF ルール value をあらわす Expression です.
 * RFC 7159 で定義されている以下のフォーマットを解釈します.
 * 
 * <pre>
 * value = false / null / true / object / array / number / string
 * </pre>
 * 
 * @ignore
 */
class Value implements Expression
{
    /**
     * デコードした結果の値です.
     *
     * @var mixed
     */
    private $result;
    
    /**
     * 新しい Value インスタンスを構築します.
     */
    public function __construct()
    {
        $this->result = null;
    }
    
    /**
     * value の内容を解釈してその結果を $result に格納します.
     * 
     * <pre>
     * value = false / null / true / object / array / number / string
     * </pre>
     * 
     * @param  Context $context
     */
    public function handle(Context $context)
    {
        $current = $context->current();
        if ($current === "-" || Number::checkDigit($context)) {
            $number = new Number();
            $number->handle($context);
            $this->result = $number->getResult();
            return;
        }
        switch ($current) {
            case "f":
                $this->decodeLiteral($context, "false", false);
                break;
            case "n":
                $this->decodeLiteral($context, "null", null);
                break;
            case "t":
                $this->decodeLiteral($context, "true", true);
                break;
            case "[":
                $array  = new ArrayExpr();
                $array->handle($context);
                $this->result = $array->getResult();
                break;
            case "{":
                $object = new ObjectExpr();
                $object->handle($context);
                $this->result = $object->getResult();
                break;
            case '"':
                $string = new StringExpr();
                $string->handle($context);
                $this->result = $string->getResult();
                break;
            default:
                $context->throwException("Invalid value format");
        }
    }
    
    /**
     * 解析結果を返します.
     * 
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * リテラル null, true, false をデコードします.
     * 
     * @param Context $context
     * @param string  $literal
     * @param mixed   $value
     */
    private function decodeLiteral(Context $context, $literal, $value)
    {
        $count = strlen($literal);
        if ($context->getSequence($count) !== $literal) {
            $current = $context->current();
            $context->throwException("Unexpected character found ('{$current}')");
        }
        $this->result = $value;
        $context->skip($count);
    }
}
