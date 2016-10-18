<?php
/*
 * Copyright (c) 2016 @trashtoy
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
namespace Peach\Http\Header;

use Exception;
use Peach\DT\Timestamp;
use Peach\DT\Util;

/**
 * Set-Cookie ヘッダーの expires 属性を書式化するクラスです.
 * expires 属性の書式は以下の通りです.
 * <code>Wdy, DD-Mon-YY HH:MM:SS GMT</code>
 * 
 * このクラスは {@link CookieOptions} でのみ使用されます.
 * エンドユーザーがこのクラスを直接使う機会はありません.
 * 
 * @ignore
 */
class CookieExpiresFormat
{
    /**
     * このクラスを直接インスタンス化することはできません.
     */
    private function __construct() {}
    
    /**
     * 唯一の CookieExpiresFormat オブジェクトを返します.
     * 
     * @return CookieExpiresFormat
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
    
    /**
     * 指定された時刻を, 第 2 引数のタイムゾーンをベースとして
     * "Wdy, DD-Mon-YY HH:MM:SS GMT" 形式の文字列に変換します.
     * 
     * @param  Timestamp $t      変換対象の時刻
     * @param  int       $offset タイムゾーン (分単位)
     * @return string 変換結果
     */
    public function format(Timestamp $t, $offset)
    {
        return $this->handleFormat($t->add("minute", Util::cleanTimeZoneOffset($offset)));
    }
    
    /**
     * format() の具体的な実装です. 引数の時刻は GMT に変換されているものとします.
     * 
     * @param  Timestamp $t 変換対象の時刻 (GMT)
     * @return string 変換結果
     */
    private function handleFormat(Timestamp $t)
    {
        $wdy = $this->formatWeekday($t->getDay());
        $dd  = str_pad($t->get("date"), 2, "0", STR_PAD_LEFT);
        $mon = $this->formatMonth($t->get("month"));
        $yy  = str_pad($t->get("year"), 4, "0", STR_PAD_LEFT);
        $hms = $t->formatTime();
        return "{$wdy}, {$dd}-{$mon}-{$yy} {$hms} GMT";
    }
    
    /**
     * 整数の曜日と, それに対応する曜日文字列の一覧を返します.
     * 
     * @return array キーに曜日, 値にその曜日の略称が対応する配列
     * @codeCoverageIgnore
     */
    private function getWeekdayMapping()
    {
        static $weekdayList = array(
            0 => "Sun",
            1 => "Mon",
            2 => "Tue",
            3 => "Wed",
            4 => "Thu",
            5 => "Fri",
            6 => "Sat",
        );
        return $weekdayList;
    }
    
    /**
     * 指定された曜日を
     * "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
     * のいずれかに変換します.
     * 
     * @param  int $day 曜日 (0 以上 6 以下)
     * @return string   指定された曜日に対応する文字列
     */
    private function formatWeekday($day)
    {
        $weekdayList = $this->getWeekdayMapping();
        if (array_key_exists($day, $weekdayList)) {
            return $weekdayList[$day];
        }
        // @codeCoverageIgnoreStart
        throw new Exception("Invalid day: {$day}");
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * 整数の月と, それに対応する略称のマッピングを返します.
     * 
     * @return array キーに月, 値にその月の略称が対応する配列
     * @codeCoverageIgnore
     */
    private function getMonthMapping()
    {
        static $monthList = array(
            1  => "Jan",
            2  => "Feb",
            3  => "Mar",
            4  => "Apr",
            5  => "May",
            6  => "Jun",
            7  => "Jul",
            8  => "Aug",
            9  => "Sep",
            10 => "Oct",
            11 => "Nov",
            12 => "Dec",
        );
        return $monthList;
    }
    
    /**
     * 指定された月を
     * Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec
     * のいずれかに変換します.
     * 
     * @param  int $m 月 (1 以上 12 以下)
     * @return string 指定された月に対応する文字列
     */
    private function formatMonth($m)
    {
        $monthList = $this->getMonthMapping();
        if (array_key_exists($m, $monthList)) {
            return $monthList[$m];
        }
        // @codeCoverageIgnoreStart
        throw new Exception("Invalid month: {$m}");
        // @codeCoverageIgnoreEnd
    }
}
