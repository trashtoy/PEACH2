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

use InvalidArgumentException;
use Peach\DF\JsonCodec\Context;
use Peach\DF\JsonCodec\DecodeException;
use Peach\DF\JsonCodec\Root;
use Peach\Util\ArrayMap;
use Peach\Util\Strings;
use Peach\Util\Values;

/**
 * JSON 形式の文字列を扱う Codec です.
 * このクラスは {@link http://tools.ietf.org/html/rfc7159 RFC 7159}
 * の仕様に基いて JSON の decode (値への変換) と encode (値の JSON への変換)
 * を行います.
 * 
 * RFC 7159 によると JSON 文字列のエンコーディングは UTF-8, UTF-16, UTF-32
 * のいずれかであると定義されていますが, この実装は UTF-8 でエンコーディングされていることを前提とします.
 * UTF-8 以外の文字列を decode した場合はエラーとなります.
 */
class JsonCodec implements Codec
{
    /**
     * 定数 JSON_HEX_TAG に相当するオプションです.
     * 文字 %x3c (LESS-THAN SIGN) および %x3e (GREATER-THAN SIGN)
     * をそれぞれ "\u003C" および "\u003E" にエンコードします
     * 
     * @var int
     */
    const HEX_TAG  = 1;
    
    /**
     * 定数 JSON_HEX_AMP に相当するオプションです.
     * 文字 "&" を "\u0026" にエンコードします.
     * 
     * @var int
     */
    const HEX_AMP  = 2;
    
    /**
     * 定数 JSON_HEX_APOS に相当するオプションです.
     * 文字 "'" を "\u0027" にエンコードします.
     * 
     * @var int
     */
    const HEX_APOS = 4;
    
    /**
     * 定数 JSON_HEX_QUOT に相当するオプションです.
     * 文字 '"' を "\u0022" にエンコードします.
     * 
     * @var int
     */
    const HEX_QUOT = 8;
    
    /**
     * 定数 JSON_NUMERIC_CHECK に相当するオプションです.
     * 数値表現の文字列を数値としてエンコードします.
     */
    const NUMERIC_CHECK = 32;
    
    /**
     * 定数 JSON_UNESCAPED_SLASHES に相当するオプションです.
     * エンコードの際に "/" をエスケープしないようにします.
     * 
     * @var int
     */
    const UNESCAPED_SLASHES = 64;
    
    /**
     * 定数 JSON_PRETTY_PRINT に相当するオプションです.
     * object, array 形式の書式でエンコードする際に,
     * 半角スペース 4 個でインデントして整形します.
     * 
     * @var int
     */
    const PRETTY_PRINT = 128;
    
    /**
     * 定数 JSON_UNESCAPED_UNICODE に相当するオプションです.
     * エンコードの際にマルチバイト文字を UTF-8 文字として表現します.
     * 
     * @var int
     */
    const UNESCAPED_UNICODE = 256;
    
    /**
     * 定数 JSON_PRESERVE_ZERO_FRACTION に相当するオプションです.
     * float 型の値を常に float 値としてエンコードします.
     * このオプションが OFF の場合, 小数部が 0 の数値 (2.0 など) は
     * 整数としてエンコードされます.
     * 
     * @var int
     */
    const PRESERVE_ZERO_FRACTION = 1024;
    
    /**
     * {@link http://php.net/manual/function.json-decode.php json_decode()}
     * の第 2 引数に相当する, このクラス独自のオプションです.
     * このオプションが ON の場合, object 形式の値をデコードする際に配列に変換します.
     * (デフォルトでは stdClass オブジェクトとなります)
     * 
     * @var int
     */
    const OBJECT_AS_ARRAY = 1;
    
    /**
     * 定数 JSON_BIGINT_AS_STRING に相当するオプションです.
     * 巨大整数をデコードする際に, int の範囲に収まらない値を文字列に変換します.
     * (デフォルトでは float 型となります)
     * 
     * @var int
     */
    const BIGINT_AS_STRING = 2;
    
    /**
     * encode の出力内容をカスタマイズするオプションです.
     * 
     * @var ArrayMap
     */
    private $encodeOptions;
    
    /**
     * decode の出力内容をカスタマイズするオプションです.
     * 
     * @var ArrayMap
     */
    private $decodeOptions;
    
    /**
     * 文字列を encode する際に使用する Utf8Codec です.
     * 
     * @var Utf8Codec
     */
    private $utf8Codec;
    
    /**
     * 新しい JsonCodec を構築します.
     * 引数にencode および decode の出力のカスタマイズオプションを指定することが出来ます.
     * 引数は配列または整数を指定することが出来ます.
     * 
     * - 配列の場合: キーにオプション定数, 値に true または false を指定してください.
     * - 整数の場合: 各オプションのビットマスクを指定してください. 例えば JsonCodec::HEX_TAG | JsonCodec::HEX_AMP のような形式となります.
     * 
     * @var array $options 出力のカスタマイズオプション
     */
    public function __construct($encodeOptions = null, $decodeOptions = null)
    {
        $this->encodeOptions = $this->initOptions($encodeOptions);
        $this->decodeOptions = $this->initOptions($decodeOptions);
        $this->utf8Codec     = new Utf8Codec();
    }
    
    /**
     * @param  array    $options
     * @return ArrayMap
     */
    private function initOptions($options)
    {
        $result = new ArrayMap();
        if (is_int($options)) {
            return $this->initOptionsByBitMask($options);
        }
        if (!is_array($options)) {
            return $result;
        }
        
        foreach ($options as $key => $value) {
            $result->put($key, \Peach\Util\Values::boolValue($value));
        }
        return $result;
    }
    
    /**
     * ビットマスクを配列に変換します.
     * 
     * @param  int      $options オプションをあらわす整数
     * @return ArrayMap          変換後のオプション
     */
    private function initOptionsByBitMask($options)
    {
        $opt    = 1;
        $result = new ArrayMap();
        while ($options) {
            $result->put($opt, (bool) ($options % 2));
            $options >>= 1;
            $opt     <<= 1;
        }
        return $result;
    }
    
    /**
     * 指定されたエンコード用オプションが ON かどうかを調べます.
     * 
     * @param  int $code オプション (定義されている定数)
     * @return bool      指定されたオプションが ON の場合は true, それ以外は false
     */
    public function getEncodeOption($code)
    {
        return $this->encodeOptions->get($code, false);
    }
    
    /**
     * 指定されたデコード用オプションが ON かどうかを調べます.
     * 
     * @param  int $code オプション (定義されている定数)
     * @return bool      指定されたオプションが ON の場合は true, それ以外は false
     */
    public function getDecodeOption($code)
    {
        return $this->decodeOptions->get($code, false);
    }
    
    /**
     * 指定された JSON 文字列を値に変換します.
     * 
     * 引数が空白文字列 (または null, false) の場合は null を返します.
     * 
     * @param  string $text 変換対象の JSON 文字列
     * @return mixed        変換結果
     */
    public function decode($text)
    {
        if (Strings::isWhitespace($text)) {
            return null;
        }
        
        try {
            $root = new Root();
            $root->handle(new Context($text, $this->decodeOptions));
            return $root->getResult();
        } catch (DecodeException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
    
    /**
     * 指定された値を JSON 文字列に変換します.
     * 
     * @param  mixed  $var 変換対象の値
     * @return string      JSON 文字列
     */
    public function encode($var)
    {
        return $this->encodeValue($var);
    }
    
    /**
     * encode の本体の処理です.
     * 指定された値がスカラー型 (null, 真偽値, 数値, 文字列), 配列, オブジェクトのいずれかにも該当しない場合,
     * 文字列にキャストした結果を encode します.
     * 
     * @param  mixed  $var 変換対象の値
     * @return string      JSON 文字列
     * @ignore
     */
    public function encodeValue($var)
    {
        if ($var === null) {
            return "null";
        }
        if ($var === true) {
            return "true";
        }
        if ($var === false) {
            return "false";
        }
        if (is_float($var)) {
            return $this->encodeFloat($var);
        }
        if (is_integer($var)) {
            return strval($var);
        }
        if (is_string($var)) {
            return is_numeric($var) ? $this->encodeNumeric($var) : $this->encodeString($var);
        }
        if (is_array($var)) {
            return $this->checkKeySequence($var) ? $this->encodeArray($var) : $this->encodeObject($var);
        }
        if (is_object($var)) {
            $arr = (array) $var;
            return $this->encodeValue($arr);
        }
        
        return $this->encodeValue(Values::stringValue($var));
    }
    
    /**
     * 数値形式の文字列を数値としてエンコードします.
     * 
     * @param string $var
     */
    private function encodeNumeric($var)
    {
        if (!$this->getEncodeOption(self::NUMERIC_CHECK)) {
            return $this->encodeString($var);
        }
        
        $num = preg_match("/^-?[0-9]+$/", $var) ? intval($var) : floatval($var);
        return $this->encodeValue($num);
    }
    
    /**
     * float 値を文字列に変換します.
     * 
     * @param  float $var 変換対象の float 値
     * @return string     変換結果
     */
    private function encodeFloat($var)
    {
        $str = strval($var);
        if (!$this->getEncodeOption(self::PRESERVE_ZERO_FRACTION)) {
            return $str;
        }
        if (false !== strpos($str, "E")) {
            return $str;
        }
        return (floor($var) === $var) ? "{$str}.0" : $str;
    }
    
    /**
     * 配列のキーが 0, 1, 2 ... という具合に 0 から始まる整数の連続になっていた場合のみ true,
     * それ以外は false を返します.
     * 
     * @param  array $arr 変換対象の配列
     * @return bool       配列のキーが整数の連続になっていた場合のみ true
     */
    private function checkKeySequence(array $arr) {
        $i = 0;
        foreach (array_keys($arr) as $key) {
            if ($i !== $key) {
                return false;
            }
            $i++;
        }
        return true;
    }
    
    /**
     * 文字列を JSON 文字列に変換します.
     * 
     * @param  string $str 変換対象の文字列
     * @return string      JSON 文字列
     * @ignore
     */
    public function encodeString($str)
    {
        $self        = $this;
        $callback    = function ($num) use ($self) {
            return $self->encodeCodePoint($num);
        };
        $unicodeList = $this->utf8Codec->decode($str);
        return '"' . implode("", array_map($callback, $unicodeList)) . '"';
    }
    
    /**
     * 指定された Unicode 符号点を JSON 文字に変換します.
     * 
     * @param  int $num Unicode 符号点
     * @return string   指定された Unicode 符号点に対応する文字列
     * @ignore
     */
    public function encodeCodePoint($num)
    {
        // @codeCoverageIgnoreStart
        static $hexList = array(
            0x3C => self::HEX_TAG,
            0x3E => self::HEX_TAG,
            0x26 => self::HEX_AMP,
            0x27 => self::HEX_APOS,
            0x22 => self::HEX_QUOT,
        );
        static $encodeList = array(
            0x22 => "\\\"",
            0x5C => "\\\\",
            0x08 => "\\b",
            0x0C => "\\f",
            0x0A => "\\n",
            0x0D => "\\r",
            0x09 => "\\t",
        );
        // @codeCoverageIgnoreEnd
        
        if (array_key_exists($num, $hexList) && $this->getEncodeOption($hexList[$num])) {
            return "\\u00" . strtoupper(dechex($num));
        }
        if (array_key_exists($num, $encodeList)) {
            return $encodeList[$num];
        }
        if ($num === 0x2F) {
            return $this->getEncodeOption(self::UNESCAPED_SLASHES) ? "/" : "\\/";
        }
        if (0x20 <= $num && $num < 0x80) {
            return chr($num);
        }
        if (0x80 <= $num && $this->getEncodeOption(self::UNESCAPED_UNICODE)) {
            return $this->utf8Codec->encode($num);
        }
        return "\\u" . str_pad(dechex($num), 4, "0", STR_PAD_LEFT);
    }
    
    /**
     * 指定された配列を JSON の array 表記に変換します.
     * オプション PRETTY_PRINT が有効化されている場合,
     * json_encode の JSON_PRETTY_PRINT と同様に半角スペース 4 個と改行文字で整形します.
     * 
     * @param  array  $arr 変換対象
     * @return string      JSON 文字列
     */
    private function encodeArray(array $arr)
    {
        $prettyPrintEnabled = $this->getEncodeOption(self::PRETTY_PRINT);
        
        $indent   = $prettyPrintEnabled ? PHP_EOL . "    " : "";
        $start    = "[" . $indent;
        $end      = $prettyPrintEnabled ? PHP_EOL . "]" : "]";
        $self     = $this;
        $callback = function ($value) use ($self, $prettyPrintEnabled, $indent) {
            $valueResult = $self->encodeValue($value);
            return $prettyPrintEnabled ? str_replace(PHP_EOL, $indent, $valueResult) : $valueResult;
        };
        return $start . implode("," . $indent, array_map($callback, $arr)) . $end;
    }
    
    /**
     * 指定された配列を JSON の object 表記に変換します.
     * オプション PRETTY_PRINT が有効化されている場合,
     * json_encode の JSON_PRETTY_PRINT と同様に半角スペース 4 個と改行文字で整形します.
     * 
     * @param  array  $arr 変換対象
     * @return string      JSON 文字列
     */
    private function encodeObject(array $arr)
    {
        $prettyPrintEnabled = $this->getEncodeOption(self::PRETTY_PRINT);
        
        $indent   = $prettyPrintEnabled ? PHP_EOL . "    " : "";
        $start    = "{" . $indent;
        $end      = $prettyPrintEnabled ? PHP_EOL . "}" : "}";
        $self     = $this;
        $callback = function ($key, $value) use ($self, $prettyPrintEnabled, $indent) {
            $coron       = $prettyPrintEnabled ? ": " : ":";
            $valueResult = $self->encodeValue($value);
            $valueJson   = $prettyPrintEnabled ? str_replace(PHP_EOL, $indent, $valueResult) : $valueResult;
            return $self->encodeString($key) . $coron . $valueJson;
        };
        return $start . implode("," . $indent, array_map($callback, array_keys($arr), array_values($arr))) . $end;
    }
}
