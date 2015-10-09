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
namespace Peach\Http\Body;

use Peach\DF\Codec;
use Peach\Http\BodyRenderer;

/**
 * Codec オブジェクトの Adapter として機能する Renderer クラスです.
 * このクラスの render メソッドは, インスタンス初期化時に指定した Codec オブジェクトの encode()
 * メソッドを実行して値を文字列に変換します.
 */
class CodecRenderer implements BodyRenderer
{
    /**
     *
     * @var Codec
     */
    private $codec;
    
    /**
     * 指定された Codec オブジェクトを使って render を行う CodecRenderer オブジェクトを構築します.
     * 
     * @param Codec $codec
     */
    public function __construct(Codec $codec)
    {
        $this->codec = $codec;
    }
    
    /**
     * このオブジェクトに登録されている Codec を使用して引数の値を文字列に変換します.
     * 
     * @param  mixed $var 変換対象の値
     * @return string     変換結果
     */
    public function render($var)
    {
        return $this->codec->encode($var);
    }
}
