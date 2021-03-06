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

use stdClass;
use Peach\DF\JsonCodec;

/**
 * JSON の BNF ルール object をあらわす Expression です.
 * RFC 7159 で定義されている以下のフォーマットを解釈します.
 * 
 * <pre>
 * object = begin-object [ member *( value-separator member ) ] end-object
 * </pre>
 * 
 * @ignore
 */
class ObjectExpr implements Expression
{
    /**
     * handle() の解析結果です.
     * 
     * @var mixed
     */
    private $result;
    
    /**
     * 新しい ObjectExpr オブジェクトを構築します.
     */
    public function __construct()
    {
        $this->result = null;
    }
    
    /**
     * 現在の Context から object 部分を読み込みます.
     * 
     * @param Context $context
     */
    public function handle(Context $context)
    {
        $beginObject = new StructuralChar(array("{"));
        $beginObject->handle($context);
        
        $container = $this->getContainer($context);
        if ($context->current() === "}") {
            $endObject = new StructuralChar(array("}"));
            $endObject->handle($context);
            $this->result = $container->getResult();
            return;
        }
        
        while (true) {
            if ($context->current() === "}") {
                throw $context->createException("Closing bracket after comma is not permitted");
            }
            $member = new Member();
            $member->handle($context);
            $container->setMember($member);
            
            $struct = new StructuralChar(array(",", "}"));
            $struct->handle($context);
            if ($struct->getResult() === "}") {
                $this->result = $container->getResult();
                break;
            }
        }
    }
    
    /**
     * handle() の結果を配列または stdClass として返します.
     * 
     * @return mixed OBJECT_AS_ARRAY オプションが ON の場合は配列, OFF の場合は stdClass オブジェクト
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * 引数の Context に指定されているオプションに応じた ArrayContainer オブジェクトを返します.
     * 
     * @param  Context $context
     * @return ObjectExpr_Container
     */
    private function getContainer(Context $context)
    {
        $asArray = $context->getOption(JsonCodec::OBJECT_AS_ARRAY);
        return $asArray ? new ObjectExpr_ArrayContainer() : new ObjectExpr_StdClassContainer();
    }
}

/**
 * decode 後の object の型 (配列または stdClass) を制御するためのインタフェースです
 * 
 * @ignore
 */
interface ObjectExpr_Container
{
    /**
     * 解析結果を返します
     * 
     * @return mixed 解析結果
     */
    public function getResult();

    /**
     * 指定された member をこの object に追加します.
     * 
     * @param Member $member
     */
    public function setMember(Member $member);
}

/**
 * object を配列として表現する ObjectExpr_Container です.
 * 
 * @ignore
 */
class ObjectExpr_ArrayContainer implements ObjectExpr_Container
{
    /**
     * 解析結果をあらわす配列です.
     * 
     * @var array
     */
    private $result;
    
    /**
     * 新しいインスタンスを生成します.
     */
    public function __construct()
    {
        $this->result = array();
    }
    
    /**
     * 解析結果を配列として返します.
     * 
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * 指定された member をこの object に追加します.
     * 
     * @param Member $member
     */
    public function setMember(Member $member)
    {
        $key = $member->getKey();
        $this->result[$key] = $member->getValue();
    }
}

/**
 * object を stdClass として表現する ObjectExpr_Container です.
 * 
 * @ignore
 */
class ObjectExpr_StdClassContainer implements ObjectExpr_Container
{
    /**
     * 解析結果をあらわす stdClass です.
     * 
     * @var stdClass
     */
    private $result;
    
    /**
     * 新しいインスタンスを生成します.
     */
    public function __construct()
    {
        $this->result = new stdClass();
    }
    
    /**
     * 解析結果を stdClass オブジェクトとして返します.
     * 
     * @return stdClass
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * 指定された member をこの object に追加します.
     * 
     * @param Member $member
     */
    public function setMember(Member $member)
    {
        $key = $member->getKey();
        $this->result->$key = $member->getValue();
    }
}
