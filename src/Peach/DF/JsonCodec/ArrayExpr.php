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
 * JSON の BNF ルール array をあらわす Expression です.
 * RFC 7159 で定義されている以下のフォーマットを解釈します.
 * 
 * <pre>
 * array = begin-array [ value *( value-separator value ) ] end-array
 * </pre>
 * 
 * @ignore
 */
class ArrayExpr implements Expression
{
    /**
     *
     * @var array
     */
    private $result;
    
    public function __construct()
    {
        $this->result = null;
    }
    
    /**
     * 
     * @param Context $context
     */
    public function handle(Context $context)
    {
        $beginArray = new StructuralChar(array("["));
        $beginArray->handle($context);
        
        if ($context->current() === "]") {
            $endArray = new StructuralChar(array("]"));
            $endArray->handle($context);
            $this->result = array();
            return;
        }
        
        $result = array();
        while (true) {
            $value = new Value();
            $value->handle($context);
            $result[] = $value->getResult();
            
            $struct = new StructuralChar(array(",", "]"));
            $struct->handle($context);
            if ($struct->getResult() === "]") {
                $this->result = $result;
                break;
            }
        }
    }
    
    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
}
