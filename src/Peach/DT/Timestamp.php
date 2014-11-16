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
use Peach\Util\Map;
use Peach\Util\ArrayMap;

/**
 * TIMESTAMP 型の時間オブジェクトです.
 * このクラスは年・月・日・時・分・秒のフィールドをサポートします.
 */
class Timestamp extends Datetime
{
    /**
     * 秒を表す整数です.
     * @var int
     */
    protected $second = 0;
    
    /**
     * 実行時の Timestamp オブジェクトを返します.
     * @return Timestamp
     */
    public static function now()
    {
        $time  = time();
        $year  = date("Y", $time);
        $month = date("n", $time);
        $date  = date("d", $time);
        $hour  = date("H", $time);
        $min   = date("i", $time);
        $sec   = date("s", $time);
        return new self($year, $month, $date, $hour, $min, $sec);
    }
    
    /**
     * 指定されたテキストを解析して Timestamp オブジェクトに変換します.
     * $format が指定されていない場合は {@link W3cDatetimeFormat::getInstance}
     * を使って解析を行います.
     * ("YYYY-MM-DD hh:mm:ss" 形式の文字列を受理します.
     * 日付と時刻のセパレータは, 数字以外の ASCII 1 文字であれば何でも構いません.)
     * 
     * @param  string    $text   変換対象の文字列
     * @param  Format    $format 変換に使用するフォーマット
     * @return Timestamp         変換結果
     */
    public static function parse($text, Format $format = null)
    {
        if (!isset($format)) {
            $format = W3cDatetimeFormat::getInstance();
        }
        return $format->parseTimestamp($text);
    }
    
    /**
     * 与えられた時刻を表現する Timestamp オブジェクトを構築します.
     * 
     * @param int $year  年
     * @param int $month 月
     * @param int $date  日
     * @param int $hour  時
     * @param int $min   分
     * @param int $sec   秒
     */
    public function __construct($year, $month, $date, $hour, $min, $sec)
    {
        $fields = new ArrayMap();
        $fields->put(self::$YEAR,   intval($year));
        $fields->put(self::$MONTH,  intval($month));
        $fields->put(self::$DATE,   intval($date));
        $fields->put(self::$HOUR,   intval($hour));
        $fields->put(self::$MINUTE, intval($min));
        $fields->put(self::$SECOND, intval($sec));
        $this->init($fields);
    }
    
    /**
     * このオブジェクトの型 {@link Time::TYPE_TIMESTAMP} を返します.
     * @return int Time::TYPE_TIMESTAMP
     */
    public function getType()
    {
        return self::TYPE_TIMESTAMP;
    }
    
    /**
     * @ignore
     */
    protected function init(Map $fields)
    {
        parent::init($fields);
        $this->second = $fields->get(self::$SECOND);
    }
    
    /**
     * この時間と指定された時間を比較します.
     * 
     * @param  Time $time 比較対象の時間
     * @return int        この時間のほうが過去の場合は負の値, 未来の場合は正の値, それ以外は 0
     * @ignore
     */
    protected function compareFields(Time $time)
    {
        $c = parent::compareFields($time);
        if (!isset($c) || $c != 0) {
            return $c;
        }
        $className = __CLASS__;
        if ($time instanceof $className) {
            return $this->second - $time->second;
        } else {
            $s = $time->get("second");
            return ($s !== $this->second) ? $this->second - $s : 0;
        }
    }
    
    /**
     * 時刻の不整合を調整します.
     * @ignore
     */
    protected function adjust(Map $fields)
    {
        parent::adjust($fields);
        $adjuster = $this->getAdjuster();
        $second   = $fields->get(self::$SECOND);
        if ($second < 0) {
            $adjuster->moveDown($fields);
        } else if (59 < $second) {
            $adjuster->moveUp($fields);
        } else {
            return;
        }
        
        $this->adjust($fields);
    }
    
    /**
     * (non-PHPdoc)
     * @see Time::newInstance()
     * @ignore
     */
    protected function newInstance(Map $fields)
    {
        $year  = $fields->get(self::$YEAR);
        $month = $fields->get(self::$MONTH);
        $date  = $fields->get(self::$DATE);
        $hour  = $fields->get(self::$HOUR);
        $min   = $fields->get(self::$MINUTE);
        $sec   = $fields->get(self::$SECOND);
        return new self($year, $month, $date, $hour, $min, $sec);
    }
    
    /**
     * @ignore
     */
    protected function handleFormat(Format $format)
    {
        return $format->formatTimestamp($this);
    }
    
    /**
     * このオブジェクトの時刻部分の文字列を "hh:mm:ss" 形式で返します.
     * @return string "hh:mm:ss" 形式の文字列
     */
    public function formatTime()
    {
        $format = parent::formatTime();
        $sec    = str_pad($this->second, 2, '0', STR_PAD_LEFT);
        return $format . ":" . $sec;
    }
    
    /**
     * このオブジェクトを Timestamp 型にキャストします.
     * 返り値はこのオブジェクトのクローンです.
     *
     * @return Timestamp このオブジェクトの Timestamp 表現
     */
    public function toTimestamp()
    {
        return new Timestamp($this->year, $this->month, $this->date, $this->hour, $this->minute, $this->second);
    }
    
    /**
     * このクラスのフィールドを調整するための FieldAdjuster を返します.
     * @return FieldAdjuster
     */
    private function getAdjuster()
    {
        static $adjuster = null;
        if (!isset($adjuster)) {
            $adjuster = new FieldAdjuster(self::$SECOND, self::$MINUTE, 0, 59);
        }
        return $adjuster;
    }
}
