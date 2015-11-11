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

class Request
{
    /**
     * クエリ部分を除いたパス文字列です.
     * @var string
     */
    private $path;
    
    /**
     * PHP の $_GET に相当するパラメータです.
     * @var array
     */
    private $queryParameters;
    
    /**
     * PHP の $_POST に相当するパラメータです.
     * @var array
     */
    private $postParameters;
    
    /**
     * HeaderField 型オブジェクトの配列です.
     * @var HeaderField[]
     */
    private $headerList;
    
    public function __construct()
    {
        $this->path = null;
        $this->queryParameters = array();
        $this->postParameters  = array();
        $this->headerList      = new ArrayMap();
    }
    
    /**
     * 指定された名前のヘッダーを取得します.
     * 存在しない場合は null を返します.
     * 
     * @param  string $name ヘッダー名
     * @return HeaderField   指定されたヘッダーに該当する HeaderField オブジェクト
     */
    public function getHeader($name)
    {
        return $this->headerList->get(strtolower($name));
    }
    
    /**
     * この Request が持つヘッダーの一覧を取得します.
     * 
     * @return HeaderField[] この Request に定義されている HeaderField のリスト
     * @todo 実装する
     */
    public function getHeaderList()
    {
        return array();
    }
    
    /**
     * 指定されたヘッダーをこの Request に設定します.
     * 
     * @param HeaderField $item
     */
    public function setHeader(HeaderField $item)
    {
        $name = strtolower($item->getName());
        $this->headerList->put($name, $item);
    }
    
    /**
     * 指定された名前の HeaderField が存在するかどうか調べます.
     * @param  string $name ヘッダー名
     * @return bool         指定された名前の HeaderField が存在する場合のみ true
     */
    public function hasHeader($name)
    {
        return $this->headerList->containsKey(strtolower($name));
    }
    
    /**
     * この Request が malformed かどうかを判断します.
     * 
     * @todo 実装する
     */
    public function isMalformed()
    {
        return false;
    }
}
