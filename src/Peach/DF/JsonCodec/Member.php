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

/**
 * JSON の BNF ルール member をあらわす Expression です.
 * RFC 7159 で定義されている以下のフォーマットを解釈します.
 * 
 * <pre>
 * member = string name-separator value
 * </pre>
 * 
 * @ignore
 */
class Member implements Expression
{
    /**
     * handle() の結果得られた object のキーです.
     * 
     * @var string
     */
    private $key;
    
    /**
     * handle() の結果得られた object の値です.
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * 新しい Member オブジェクトを構築します.
     */
    public function __construct()
    {
        $this->key   = null;
        $this->value = null;
    }
    
    /**
     * 現在の Context から member 部分を読み込みます.
     * 
     * @param Context $context
     */
    public function handle(Context $context)
    {
        $string = new StringExpr();
        $string->handle($context);
        $this->key = $string->getResult();
        
        $nameSeparator = new StructuralChar(array(":"));
        $nameSeparator->handle($context);
        
        $value = new Value();
        $value->handle($context);
        $this->value = $value->getResult();
    }
    
    /**
     * handle() の結果得られた object のキーを返します.
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * handle() の結果得られた object の値を返します.
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
