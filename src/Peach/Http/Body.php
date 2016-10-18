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
namespace Peach\Http;

/**
 * HTTP リクエストおよびレスポンスの Body 部分を表現するクラスです.
 */
class Body
{
    /**
     *
     * @var mixed
     */
    private $value;
    
    /**
     *
     * @var BodyRenderer
     */
    private $renderer;
    
    /**
     * 指定された出力対象のデータおよび BodyRenderer から成る Body オブジェクトを構築します.
     * 
     * @param mixed $value 出力対象のデータ
     * @param BodyRenderer $renderer 出力対象のデータを HTTP メッセージボディに変換するための BodyRenderer オブジェクト
     */
    public function __construct($value, BodyRenderer $renderer)
    {
        $this->value      = $value;
        $this->renderer   = $renderer;
    }
    
    /**
     * この Response が返却する値の生データ (内部表現) を返します.
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * この Body オブジェクトを文字列に変換するための Renderer を返します.
     * 
     * @return BodyRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
}
