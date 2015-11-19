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
namespace Peach\Http\Header;

use Peach\Http\HeaderField;
use Peach\Http\Util;

/**
 * Accept, Accept-Language, Accept-Encoding など, quality value を値に持つヘッダーを表現するクラスです.
 */
class QualityValues implements HeaderField
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var array
     */
    private $qvalueList;
    
    /**
     * 指定されたヘッダー名および quality value のリストを持つ QualityValues インスタンスを構築します.
     * 
     * 第 2 引数には以下のようなフォーマットの配列を指定してください.
     * <code>
     * array("ja" => 1.0, "en-US" => 0.9, "en-GB" => 0.8, "en" => 0.7)
     * </code>
     * 
     * @param type $name
     * @param array $qvalueList
     */
    public function __construct($name, array $qvalueList)
    {
        Util::validateHeaderName($name);
        foreach ($qvalueList as $key => $value) {
            $this->validateQvalue($key, $value);
        }
        arsort($qvalueList);
        $this->name       = $name;
        $this->qvalueList = $qvalueList;
    }
    
    /**
     * それぞれの qvalue の値が 0 以上 1 以下の小数となっていることを確認します.
     * 
     * @param string $key
     * @param string $value
     * @throws \InvalidArgumentException
     */
    private function validateQvalue($key, $value)
    {
        if (!preg_match("/\\A[a-zA-Z0-9_\\-\\/\\+\\*]+\\z/", $key)) {
            throw new \InvalidArgumentException("Invalid qvalue name: '{$key}'");
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Each qvalue must be a real number");
        }
        if ($value < 0 || 1.0 < $value) {
            throw new \InvalidArgumentException("Each qvalue must be in the range 0 through 1");
        }
    }
    
    /**
     * 
     * @return string
     */
    public function format()
    {
        $callback = function ($key, $value) {
            return $value === 1.0 ? $key : "{$key};q={$value}";
        };
        $qvalueList = $this->qvalueList;
        return implode(",", array_map($callback, array_keys($qvalueList), array_values($qvalueList)));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
