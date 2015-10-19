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

use InvalidArgumentException;

class Util
{
    /**
     * このクラスはインスタンス化できません
     */
    private function __construct() {}
    
    /**
     * 指定された文字列が HTTP ヘッダーとして妥当かどうかを検証します.
     * 文字列が半角アルファベット・数字・ハイフンから成る場合のみ妥当とします.
     * 妥当な文字列でない場合は InvalidArgumentException をスローします.
     * 
     * @param string $name
     * @throws InvalidArgumentException
     */
    public static function validateHeaderName($name)
    {
        // @codeCoverageIgnoreStart
        static $whiteList = array(
            ":authority",
            ":path",
            ":method",
            ":scheme",
            ":status",
        );
        // @codeCoverageIgnoreEnd
        if (in_array($name, $whiteList, true)) {
            return;
        }
        if (!preg_match("/\\A[a-zA-Z0-9\\-]+\\z/", $name)) {
            throw new InvalidArgumentException("{$name} is not a valid header name");
        }
    }
}
