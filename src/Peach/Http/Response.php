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

use Peach\Util\ArrayMap;

class Response
{
    /**
     * HeaderItem 型オブジェクトを格納する ArrayMap です.
     * @var ArrayMap
     */
    private $headerList;
    
    /**
     *
     * @var MessageBody
     */
    private $messageBody;
    
    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->headerList  = new ArrayMap();
        $this->messageBody = null;
    }
    
    /**
     * 指定された名前のヘッダーを取得します.
     * 存在しない場合は null を返します.
     * 
     * @param  string $name ヘッダー名
     * @return HeaderItem   指定されたヘッダーに該当する HeaderItem オブジェクト
     */
    public function getHeader($name)
    {
        return $this->headerList->get(strtolower($name));
    }
    
    /**
     * 指定されたヘッダーをこの Response に設定します.
     * 
     * @param HeaderItem $item
     */
    public function setHeader(HeaderItem $item)
    {
        $name = strtolower($item->getName());
        $this->headerList->put($name, $item);
    }
    
    /**
     * 指定された名前の HeaderItem が存在するかどうか調べます.
     * @param  string $name ヘッダー名
     * @return bool         指定された名前の HeaderItem が存在する場合のみ true
     */
    public function hasHeader($name)
    {
        return $this->headerList->containsKey(strtolower($name));
    }
}
