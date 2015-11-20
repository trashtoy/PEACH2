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
use Peach\DT\Timestamp;
use Peach\DT\HttpDateFormat;

/**
 * Last-Modified や If-Modified-Since など, HTTP-date 形式の値を持つヘッダーです.
 */
class HttpDate implements HeaderField
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var Timestamp
     */
    private $time;
    
    /**
     *
     * @var HttpDateFormat
     */
    private $format;
    
    /**
     * 
     * @param string         $name
     * @param Timestamp      $time
     * @param HttpDateFormat $format
     */
    public function __construct($name, Timestamp $time, HttpDateFormat $format = null)
    {
        Util::validateHeaderName($name);
        $this->name   = $name;
        $this->time   = $time;
        $this->format = ($format === null) ? HttpDateFormat::getInstance() : $format;
    }
    
    /**
     * 
     * @return string
     */
    public function format()
    {
        return $this->time->format($this->format);
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @return Timestamp
     */
    public function getValue()
    {
        return $this->time;
    }
}
