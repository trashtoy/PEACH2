<?php
/*
 * Copyright (c) 2014 @trashtoy
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
 * @since  2.0.0
 */
namespace Peach\DT;

/**
 * {@link http://www.w3.org/TR/NOTE-datetime W3CDTF} と時間オブジェクトの相互変換を行うフォーマットです.
 * 本来の W3CDTF は日付と時刻の間に "T" が入りますが (例: "YYYY-MM-DDThh:mm:ss")
 * このクラスは SQL などで用いられる慣用表現 (例: "YYYY-MM-DD hh:mm:ss") のパースもできます.
 * 
 * parse メソッドは, 文字列の先頭が W3CDTF の書式にマッチするかどうかで妥当性をチェックします.
 * "2012-05-21T07:30:00" のような文字列は
 * parseDate, parseDatetime, parseTimestamp のすべてのメソッドで受け付けますが,
 * "2012-05-21" のような文字列は parseDate だけしか受け付けません.
 * 
 * このクラスはタイムゾーンを適切に扱うことが出来ます.
 * parse 系メソッドでは, 時間オブジェクトに変換する際に内部のタイムゾーンに合わせて時刻を調整します.
 * 例えばシステム時刻が UTC+9 として, GMT の時間文字列 "2012-05-20T22:30Z" を parse した場合
 * 9 時間進ませた時間オブジェクト (new Datetime(2012, 5, 21, 7, 30) に等価) を生成します.
 * 
 * 時間オブジェクトを書式化する際は, 出力用の時差に合わせた文字列を生成します.
 * 例えば内部のタイムゾーンが UTC+9, 出力用のタイムゾーンが UTC+5 に設定されているものとします.
 * この場合, 内部のタイムゾーンと出力用のタイムゾーンの時差が -4 時間となるため,
 * new Datetime(2012, 5, 21, 7, 30) の時間オブジェクトを書式化した結果は
 * "2012-05-21T03:30+5:00" となります.
 */
class W3cDatetimeFormat implements Format
{
    /**
     * タイムゾーンを扱うのみ TRUE となります
     * @var bool
     */
    private $usingTz;
    
    /**
     * システム時刻の時差です (単位は分)
     * @var int
     */
    private $internalOffset;
    
    /**
     * フォーマットの時差です (単位は分)
     * @var int
     */
    private $externalOffset;
    
    /**
     * 日付文字列のパターンです.
     * "YYYY-MM-DD" のような形式にマッチします.
     * 
     * @var string
     */
    private static $datePattern = "[0-9]{4}[^0-9][0-9]{2}[^0-9][0-9]{2}";
    
    /**
     * タイムゾーンの文字列のパターンです. 以下の形式にマッチします.
     * 
     * - "Z"
     * - "-hh:mm"
     * - "+hh:mm"
     * - 空文字列
     * 
     * @var string
     */
    private static $timeZonePattern = "(Z|[\\-\\+][0-9]{2}:[0-9]{2})?";
    
    /**
     * 指定されたタイムゾーンの条件で, 新しいフォーマットを構築します.
     * タイムゾーンを扱わない場合は, コンストラクタの代わりに
     * {@link W3cDatetimeFormat::getInstance} を使って下さい.
     * 
     * 引数 $externalOffset を指定することで, 
     * 書式化する際のタイムゾーンの値を設定することが出来ます.
     * (デフォルトはシステム時刻のタイムゾーンとなります)
     * さらに $internalOffset を指定することで,
     * システム時刻のタイムゾーンとして任意の値を指定することも出来ます.
     * 
     * $externalOffset と $internalOffset を省略した場合, デフォルト値としてシステム時刻の設定
     * ({@link Util::getTimeZoneOffset}) と同じ値が指定されます.
     * UTC+1 以上の時差を設定する場合は負の値,
     * UTC-1 以下の時差を設定する場合は正の値を指定してください.
     * 
     * 以下に, $externalOffset と $internalOffset の組み合わせによる動作例を示します.
     * 
     * <code>
     * // 書式・システム時刻共に UTC+9
     * $f1 = new W3cDatetimeFormat(-540, -540);
     * 
     * // 書式は GMT, システム時刻は UTC+9
     * $f2 = new W3cDatetimeFormat(0,    -540);
     * 
     * // 書式は UTC+9, システム時刻は GMT
     * $f3 = new W3cDatetimeFormat(-540,    0);
     * 
     * // 2012-05-21T07:30 の時間オブジェクトを生成
     * $d = new Datetime(2012, 5, 21, 7, 30);
     * var_dump($d->format($f1)); // "2012-05-21T07:30+09:00"
     * var_dump($d->format($f2)); // "2012-05-20T22:30Z"      (UTC+9 の時刻を GMT として書式化)
     * var_dump($d->format($f3)); // "2012-05-21T16:30+09:00" (GMT の時刻を UTC+9 として書式化)
     * </code>
     * 
     * @param int  $externalOffset フォーマットの時差 (単位は分, 省略した場合はシステム設定の値を使用)
     * @param int  $internalOffset システム時刻の時差 (単位は分, 省略した場合はシステム設定の値を使用)
     */
    public function __construct($externalOffset = null, $internalOffset = null)
    {
        // @codeCoverageIgnoreStart
        if ($externalOffset === false) {
            $this->usingTz        = false;
            $this->externalOffset = null;
            $this->internalOffset = null;
            return;
        }
        // @codeCoverageIgnoreEnd
        
        $this->usingTz        = true;
        $this->externalOffset = Util::cleanTimeZoneOffset($externalOffset);
        $this->internalOffset = Util::cleanTimeZoneOffset($internalOffset);
    }
    
    /**
     * デフォルトのインスタンスを返します.
     * このメソッドから生成されたオブジェクトは, parse の際にタイムゾーンを一切考慮しません.
     * また, 書式化する際にタイムゾーン文字列を付与しません.
     * 
     * @return W3cDatetimeFormat タイムゾーンに対応しないインスタンス
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self(false);
        }
        return $instance;
    }
    
    /**
     * "YYYY-MM-DD" 形式の文字列を解析します.
     * 
     * @param  string $format 解析対象の文字列
     * @return Date           解析結果
     * @see Format::parseDate()
     */
    public function parseDate($format)
    {
        $dp = self::$datePattern;
        if (preg_match("/^{$dp}/", $format)) {
            $year  = substr($format, 0, 4);
            $month = substr($format, 5, 2);
            $date  = substr($format, 8, 2);
            return new Date($year, $month, $date);
        } else {
            throw $this->createFormatException($format, "YYYY-MM-DD");
        }
    }
    
    /**
     * "YYYY-MM-DDThh:mm" 形式の文字列を解析します.
     * 文字列の末尾にタイムゾーン (+hh:mm や -hh:mm など) を含む場合は, システム時刻への変換を行います.
     * 
     * @param  string $format 解析対象の文字列
     * @return Datetime       解析結果
     * @see Format::parseDatetime()
     */
    public function parseDatetime($format)
    {
        $dp     = self::$datePattern;
        $tzp    = self::$timeZonePattern;
        $result = null;
        if (!preg_match("/^{$dp}[^0-9][0-9]{2}:[0-9]{2}{$tzp}/", $format, $result)) {
            throw $this->createFormatException($format, "YYYY-MM-DD hh:mm[timezone]");
        }
        
        $year   = substr($format, 0,  4);
        $month  = substr($format, 5,  2);
        $date   = substr($format, 8,  2);
        $hour   = substr($format, 11, 2);
        $min    = substr($format, 14, 2);
        $obj    = new Datetime($year, $month, $date, $hour, $min);
        return ($this->usingTz && isset($result[1])) ?
            $this->adjustFromParse($obj, $result[1]) : $obj;
    }
    
    /**
     * "YYYY-MM-DDThh:mm:ss" 形式の文字列を解析します.
     * 
     * @param  string $format 解析対象の文字列
     * @return Timestamp      解析結果
     * @see Format::parseTimestamp()
     */
    public function parseTimestamp($format)
    {
        $dp     = self::$datePattern;
        $tzp    = self::$timeZonePattern;
        $result = null;
        if (!preg_match("/^{$dp}[^0-9][0-9]{2}:[0-9]{2}:[0-9]{2}{$tzp}/", $format, $result)) {
            throw $this->createFormatException($format, "YYYY-MM-DD hh:mm:ss");
        }
        
        $year   = substr($format, 0,  4);
        $month  = substr($format, 5,  2);
        $date   = substr($format, 8,  2);
        $hour   = substr($format, 11, 2);
        $min    = substr($format, 14, 2);
        $sec    = substr($format, 17, 2);
        $obj    = new Timestamp($year, $month, $date, $hour, $min, $sec);
        return ($this->usingTz && isset($result[1])) ?
            $this->adjustFromParse($obj, $result[1]) : $obj;
    }
    
    /**
     * 指定された時間オブジェクトを "YYYY-MM-DD" 形式の文字列に変換します.
     * 
     * @param  Date $d 解析対象の Date オブジェクト
     * @return string  "YYYY-MM-DD" 形式の文字列
     * @see    Format::formatDate()
     */
    public function formatDate(Date $d)
    {
        return $d->__toString();
    }
    
    /**
     * 指定された時間オブジェクトを "YYYY-MM-DDThh:mm" 形式の文字列に変換します.
     * このインスタンスがタイムゾーンに対応している場合, 末尾にタイムゾーン文字列も付加します.
     * 
     * @param  Datetime $d 変換対象の Datetime オブジェクト
     * @return string      "YYYY-MM-DDThh:mm" 形式の文字列
     * @see    Format::formatDatetime()
     */
    public function formatDatetime(Datetime $d)
    {
        $a = $this->adjustFromFormat($d, false);
        return $this->formatDate($a->toDate()) . "T" . $a->formatTime() . $this->formatTimezone();
    }
    
    /**
     * 指定された時間オブジェクトを "YYYY-MM-DDThh:mm:ss" 形式の文字列に変換します.
     * このインスタンスがタイムゾーンに対応している場合, 末尾にタイムゾーン文字列も付加します.
     * 
     * @param  Timestamp $d 変換対象の Timestamp オブジェクト
     * @return string       "YYYY-MM-DDThh:mm:ss" 形式の文字列
     * @see    Format::formatTimestamp()
     */
    public function formatTimestamp(Timestamp $d)
    {
        return $this->formatDatetime($d);
    }
    
    /**
     * 指定された文字列が想定されたフォーマットでないことをあらわす
     * InvalidArgumentException を生成します.
     * 
     * @param string $format
     * @param string $expected
     * @return \InvalidArgumentException
     */
    private function createFormatException($format, $expected)
    {
        return new \InvalidArgumentException("Illegal format({$format}). Expected: {$expected}");
    }
    
    /**
     * タイムゾーン文字列をパースして, 時差を分単位で返します.
     * NULL が指定された場合 (タイムゾーン文字列がなかった場合),
     * このオブジェクトに設定されている externalOffset の値を返します.
     * 
     * @param  string $tz NULL, "Z", または "+h:mm", "-h:mm" 形式の文字列
     * @return int
     */
    private function parseTimezone($tz)
    {
        if ($tz === "Z") {
            return 0;
        }
        
        $coronIndex = strpos($tz, ":");
        $sign       = substr($tz, 0, 1);
        $hour       = substr($tz, 1, $coronIndex - 1);
        $minute     = substr($tz, $coronIndex + 1);
        $offset     = $hour * 60 + $minute;
        return ($sign === "-") ? $offset : - $offset;
    }
    
    /**
     * フォーマットのタイムゾーンと, 時間オブジェクトのタイムゾーンを元に
     * 指定された時間オブジェクトの補正処理を行います.
     * 
     * @param  Datetime $d  補正対象の時間オブジェクト
     * @param  string   $tz タイムゾーン文字列
     * @return Datetime     補正結果の時間オブジェクト
     */
    private function adjustFromParse(Datetime $d, $tz)
    {
        $externalOffset = $this->parseTimezone($tz);
        return $d->add("minute", $externalOffset - $this->internalOffset);
    }
    
    /**
     * このオブジェクトがタイムゾーンを利用する場合は,
     * 引数の時間オブジェクトを補正した結果を返します.
     * タイムゾーンを利用しない場合は引数をそのまま返します.
     * このメソッドは各種 format メソッドから呼ばれます.
     * 
     * @param  Datetime $d 補正対象の時間オブジェクト
     * @return Datetime    補正結果
     */
    private function adjustFromFormat(Datetime $d)
    {
        return $this->usingTz ? $d->add("minute", $this->internalOffset - $this->externalOffset) : $d;
    }
    
    /**
     * タイムゾーンを書式化します.
     * @return string
     */
    private function formatTimezone()
    {
        if (!$this->usingTz) {
            return "";
        }
        if ($this->externalOffset === 0) {
            return "Z";
        }
        
        $ext    = $this->externalOffset;
        $format = (0 < $ext) ? "-" : "+";
        $offset = abs($ext);
        $hour   = intval($offset / 60);
        $minute = intval($offset % 60);
        $format .= str_pad($hour,   2, '0', STR_PAD_LEFT);
        $format .= ':';
        $format .= str_pad($minute, 2, '0', STR_PAD_LEFT);
        return $format;
    }
}
