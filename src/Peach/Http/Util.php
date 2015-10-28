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
     * 指定された文字列が HTTP ヘッダー名として妥当かどうかを検証します.
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
    
    /**
     * 指定された文字列が HTTP ヘッダーの値として妥当かどうかを検証します.
     * 
     * {@link https://tools.ietf.org/html/rfc7230 RFC 7230} で定義された以下の ABNF に基いて妥当性の判定を行います.
     * 
     * <pre>
     * header-field   = field-name ":" OWS field-value OWS
     * 
     * field-name     = token
     * field-value    = *( field-content / obs-fold )
     * field-content  = field-vchar [ 1*( SP / HTAB ) field-vchar ]
     * field-vchar    = VCHAR / obs-text
     *
     * obs-fold       = CRLF 1*( SP / HTAB )
     * </pre>
     * 
     * @todo   実装する
     * @param  string $value
     * @throws InvalidArgumentException
     */
    public static function validateHeaderValue($value)
    {
        
    }
}
