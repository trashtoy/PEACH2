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
 */
namespace Peach\DF;

/**
 * 複数の Codec のエンコード・デコード処理を連結させるための Codec です.
 * 
 * このクラスはコンストラクタに 2 種類の Codec を指定してインスタンスを初期化します.
 * エンコードは, コンストラクタに指定した 1 番目, 2 番目の Codec の encode() を順に呼び出すことで実行されます.
 * デコードは, エンコードとは逆に 2 番目, 1 番目の順番で実行されます.
 */
class CodecChain implements Codec
{
    /**
     * 1 番目の Codec です. エンコードの際に先に処理されます.
     * 
     * @var Codec
     */
    private $first;
    
    /**
     * 2 番目の Codec です. デコードの際に先に処理されます.
     * 
     * @var Codec
     */
    private $second;
    
    /**
     * 指定された Codec を使用して変換を行う CodecChain インスタンスを生成します.
     * 
     * @param Codec $first  1 番目の Codec
     * @param Codec $second 2 番目の Codec
     */
    public function __construct(Codec $first, Codec $second)
    {
        $this->first  = $first;
        $this->second = $second;
    }
    
    /**
     * このオブジェクトに指定された Codec を使って指定された値をデコードします.
     * 
     * @param  mixed $text デコード対象の値
     * @return mixed       変換結果
     */
    public function decode($text)
    {
        return $this->first->decode($this->second->decode($text));
    }
    
    /**
     * このオブジェクトに指定された Codec を使って指定された値をエンコードします.
     * 
     * @param  mixed $var エンコード対象の値
     * @return mixed      変換結果
     */
    public function encode($var)
    {
        return $this->second->encode($this->first->encode($var));
    }
}
