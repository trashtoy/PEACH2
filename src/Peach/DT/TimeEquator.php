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
use Peach\Util\Equator;
use Peach\Util\Values;

/**
 * 時間オブジェクトの比較を行うための Equator です.
 * このクラスは, 時間オブジェクトをキーとした {@link \Peach\Util\HashMap}
 * を構築する際に使用してください.
 */
class TimeEquator implements Equator
{
    /**
     * 比較対象のフィールドの配列です.
     * array("year", "month", "date")
     * のような文字列型の配列となります.
     * 
     * @var array
     */
    private $fields;
    
    /**
     * 比較対象のフィールドを指定して, 新しい TimeEquator オブジェクトを作成します.
     * 引数の指定方法には以下の方法があります.
     * 
     * <code>
     * new TimeEquator();
     * new TimeEquator(array("hour", "minute", "second"));
     * new TimeEquator("date");
     * new TimeEquator(Time::TYPE_DATE);
     * </code>
     * 
     * 引数なしでオブジェクトを生成した場合, このオブジェクトは
     * {@link Time::equals()} を使って比較を行います.
     * 通常は, 引数なしのコンストラクタを使う代わりに
     * {@link TimeEquator::getDefault()} を使用してください.
     * 
     * 引数に比較対象のフィールドを配列で指定した場合,
     * 指定されたフィールドすべてが等しい場合に等価とみなします.
     * 
     * 比較対象のフィールドが 1 つだけの場合, そのフィールドを文字列で指定することもできます.
     * 
     * また, 以下の定数を使用することもできます.
     * 
     * - {@link Time::TYPE_DATE}
     * - {@link Time::TYPE_DATETIME}
     * - {@link Time::TYPE_TIMESTAMP}
     * 
     * それぞれ
     * 
     * - array("year", "month", "date")
     * - array("year", "month", "date", "hour", "minute")
     * - array("year", "month", "date", "hour", "minute", "second")
     * 
     * を指定した場合と同じになります.
     * 
     * @param mixed $fields 比較対象のフィールド一覧
     */
    public function __construct($fields = null)
    {
        $this->fields = $this->initFields($fields);
    }
    
    /**
     * このオブジェクトの比較対象フィールド一覧を初期化します.
     * 
     * @param  mixed $fields
     * @return array
     */
    private function initFields($fields)
    {
        switch ($fields) {
            case Time::TYPE_DATE:
                return $this->initFields(array("year", "month", "date"));
            case Time::TYPE_DATETIME:
                return $this->initFields(array("year", "month", "date", "hour", "minute"));
            case Time::TYPE_TIMESTAMP:
                return $this->initFields(array("year", "month", "date", "hour", "minute", "second"));
        }

        if (is_array($fields)) {
            return count($fields) ? $fields : null;
        }
        if (is_string($fields)) {
            return array($fields);
        }
        
        return null;
    }
    
    /**
     * デフォルトの Equator オブジェクトを返します.
     * このオブジェクトは {@link Time::equals()} を使って等値性を調べます.
     * @return TimeEquator
     */
    public static function getDefault()
    {
        static $instance = null;
        if (!isset($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * 指定された 2 つの時間オブジェクトが等しいかどうか調べます.
     * この Equator に設定されているフィールドについて比較を行い,
     * 全て等しい場合のみ TRUE を返します.
     * 
     * @param  Time $var1 比較対象の時間オブジェクト
     * @param  Time $var2 比較対象の時間オブジェクト
     * @return bool       2 つの時間オブジェクトが等しいと判断された場合のみ TRUE
     * @throws \InvalidArgumentException 引数に Time インスタンス以外の値が指定された場合
     */
    public function equate($var1, $var2)
    {
        if (!($var1 instanceof Time) || !($var2 instanceof Time)) {
            $arg1 = Values::getType($var1);
            $arg2 = Values::getType($var2);
            throw new \InvalidArgumentException("arguments must be Time instance.({$arg1}, {$arg2})");
        }

        $fields = $this->fields;
        if (isset($fields)) {
            foreach ($fields as $field) {
                if ($var1->get($field) !== $var2->get($field)) {
                    return false;
                }
            }
            return true;
        } else {
            return $var1->equals($var2);
        }
    }

    /**
     * 年・月・日・時・分・秒の各フィールドからハッシュ値を算出します.
     * 
     * @param  mixed $var
     * @return int        ハッシュ値
     * @throws \InvalidArgumentException 引数が Time インスタンスでなかった場合
     */
    public function hashCode($var)
    {
        if (!($var instanceof Time)) {
            $type = Values::getType($var);
            throw new \InvalidArgumentException("The value must be Time instance.({$type})");
        }
        
        return
            $var->get("year")              +
            $var->get("month")  *       31 +  // 31^1
            $var->get("date")   *      961 +  // 31^2
            $var->get("hour")   *    29791 +  // 31^3
            $var->get("minute") *   923521 +  // 31^4
            $var->get("second") * 28629151;   // 31^5
    }
}
