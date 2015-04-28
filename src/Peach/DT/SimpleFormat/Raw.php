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
 * @since  2.0.1
 */
namespace Peach\DT\SimpleFormat;

use Peach\Util\ArrayMap;
use Peach\Util\Strings;

/**
 * 文字列の先頭が指定された文字列に合致するか調べる Pattern です.
 * 
 * @ignore
 */
class Raw implements Pattern
{
    /**
     * 候補の一覧です.
     * @var array
     */
    private $candidates;
    
    /**
     * 文字列の候補を指定して新しい Raw オブジェクトを生成します.
     * @param array $candidates 候補の一覧
     */
    public function __construct(array $candidates)
    {
        $this->candidates = $candidates;
    }
    
    /**
     * このメソッドは何も行いません.
     * 
     * @param ArrayMap $fields  適用対象のフィールド
     * @param string   $matched マッチした文字列
     */
    public function apply(ArrayMap $fields, $matched)
    {
        // noop
    }
    
    /**
     * 文字列の先頭が, このオブジェクトにセットされている候補一覧の中のいずれかに合致するかどうかを調べます.
     * 合致した候補文字列を返します.
     * 
     * @param  string $input 検査対象の文字列
     * @return string        合致した候補文字列
     */
    public function match($input)
    {
        foreach ($this->candidates as $candidate) {
            if (Strings::startsWith($input, $candidate)) {
                return $candidate;
            }
        }
        return null;
    }
}
