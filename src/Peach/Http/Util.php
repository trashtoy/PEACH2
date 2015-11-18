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
use Peach\Http\Header\Raw;

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
     * 妥当な文字列でない場合は InvalidArgumentException をスローします.
     * obs-text および obs-fold については RFC7230 で廃止されているため例外として処理します.
     * 
     * @param  string $value 検査対象のヘッダー値
     * @throws InvalidArgumentException 引数がヘッダー値として不正だった場合
     */
    public static function validateHeaderValue($value)
    {
        if (!self::handleValidateHeaderValue($value)) {
            throw new InvalidArgumentException("'{$value}' is not a valid header value");
        }
    }
    
    /**
     * 引数がヘッダー値として妥当な場合のみ true を返します.
     * 
     * @param  string $value
     * @return bool
     */
    private static function handleValidateHeaderValue($value)
    {
        $trimmed = trim($value);
        if ($trimmed !== $value) {
            return false;
        }
        if ($value === "") {
            return true;
        }
        $bytes = str_split($value);
        return (count($bytes) === 1) ? self::validateVCHAR($value) : self::validateBytes($bytes);
    }
    
    /**
     * 
     * @param  array $bytes
     * @return bool
     */
    private static function validateBytes($bytes)
    {
        $head = array_shift($bytes);
        if (!self::validateVCHAR($head)) {
            return false;
        }
        $tail = array_pop($bytes);
        if (!self::validateVCHAR($tail)) {
            return false;
        }
        foreach ($bytes as $chr) {
            if (!self::validateVCHAR($chr) && $chr !== " " && $chr !== "\t") {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @param  string $chr
     * @return bool
     */
    private static function validateVCHAR($chr)
    {
        $byte = ord($chr);
        return (0x21 <= $byte && $byte <= 0x7E);
    }
    
    /**
     * 指定されたヘッダー名, ヘッダー値の組み合わせから
     * HeaderField オブジェクトを構築します.
     * 
     * @param  string $name  ヘッダー名
     * @param  string $value ヘッダー値
     * @return HeaderField
     */
    public static function parseHeader($name, $value)
    {
        return new Raw($name, $value);
    }
}
