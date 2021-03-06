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

use Peach\Http\Header\NoField;
use Peach\Util\ArrayMap;

/**
 * HTTP リクエストをあらわすクラスです.
 */
class Request
{
    /**
     * クエリ部分を除いたパス文字列です.
     * @var string
     */
    private $path;
    
    /**
     * PHP の $_GET に相当するパラメータです.
     * @var ArrayMap
     */
    private $queryParameters;
    
    /**
     * PHP の $_POST に相当するパラメータです.
     * @var ArrayMap
     */
    private $postParameters;
    
    /**
     * HeaderField 型オブジェクトの配列です.
     * @var ArrayMap
     */
    private $headerList;
    
    /**
     * 空の Request インスタンスを構築します.
     */
    public function __construct()
    {
        $this->path = null;
        $this->queryParameters = new ArrayMap();
        $this->postParameters  = new ArrayMap();
        $this->headerList      = new ArrayMap();
    }
    
    /**
     * この Request のパス (URL のうち, クエリ・フラグメントを除いた部分) を設定します.
     * 
     * @param string $path セットするパス
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    /**
     * この Request のパス (URL のうち, クエリ・フラグメントを除いた部分) を返します.
     * 
     * @return string この Request のパス
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * 指定された名前のヘッダーを取得します.
     * 存在しない場合は NoField オブジェクトを返します.
     * 
     * @param  string $name ヘッダー名
     * @return HeaderField  指定されたヘッダーに該当する HeaderField オブジェクト
     */
    public function getHeader($name)
    {
        $lName  = strtolower($name);
        if ($lName === "host") {
            return $this->getHeader(":authority");
        }
        
        $header = $this->headerList->get($lName);
        return ($header instanceof HeaderField) ? $header : NoField::getInstance();
    }
    
    /**
     * この Request が持つヘッダーの一覧を取得します.
     * 
     * @return HeaderField[] この Request に定義されている HeaderField のリスト
     * @todo 実装する
     */
    public function getHeaderList()
    {
        return $this->headerList->asArray();
    }
    
    /**
     * 指定されたヘッダーをこの Request に設定します.
     * 
     * @param HeaderField $item
     */
    public function setHeader(HeaderField $item)
    {
        $name = strtolower($item->getName());
        $key  = ($name === "host") ? ":authority" : $name;
        $this->headerList->put($key, $item);
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
     * 指定された値を GET パラメータとしてセットします.
     * 引数には配列または ArrayMap オブジェクトを指定することができます.
     * 配列または ArrayMap のキーをパラメータ名, 値をそのパラメータの値とします.
     * 
     * @param array|ArrayMap $params
     */
    public function setQuery($params)
    {
        if (is_array($params)) {
            $this->setQuery(new ArrayMap($params));
            return;
        }
        $this->queryParameters->putAll($params);
    }
    
    /**
     * 指定された名前の GET パラメータを返します.
     * 第 2 引数に, そのパラメータが存在しなかった場合に返される代替値を指定することができます.
     * 第 2 引数を指定しない場合は null を返します.
     * 
     * @param  string       $name         パラメータ名
     * @param  string|array $defaultValue そのパラメータが存在しない場合の代替値. 未指定の場合は null
     * @return string|array               パラメータの値
     */
    public function getQuery($name, $defaultValue = null)
    {
        return $this->queryParameters->get($name, $defaultValue);
    }
    
    /**
     * 指定された値を POST パラメータとしてセットします.
     * 引数には配列または ArrayMap オブジェクトを指定することができます.
     * 配列または ArrayMap のキーをパラメータ名, 値をそのパラメータの値とします.
     * 
     * @param array|ArrayMap $params
     */
    public function setPost($params)
    {
        if (is_array($params)) {
            $this->setPost(new ArrayMap($params));
            return;
        }
        $this->postParameters->putAll($params);
    }
    
    /**
     * 指定された名前の POST パラメータを返します.
     * 第 2 引数に, そのパラメータが存在しなかった場合に返される代替値を指定することができます.
     * 第 2 引数を指定しない場合は null を返します.
     * 
     * @param  string       $name         パラメータ名
     * @param  string|array $defaultValue そのパラメータが存在しない場合の代替値. 未指定の場合は null
     * @return string|array               パラメータの値
     */
    public function getPost($name, $defaultValue = null)
    {
        return $this->postParameters->get($name, $defaultValue);
    }
    
    /**
     * この Request が malformed (不正な形式) かどうかを判断します.
     * 以下に挙げるヘッダーのうち, 1 つでも欠けているものがあった場合に malformed と判定します.
     * 
     * - :method
     * - :scheme
     * - :path
     * - :authority (または Host)
     * 
     * @return bool この Request が malformed と判定された場合のみ true
     */
    public function isMalformed()
    {
        $headerNames = array(":method", ":scheme", ":path", ":authority");
        foreach ($headerNames as $h) {
            if (!$this->hasHeader($h)) {
                return true;
            }
        }
        return false;
    }
}
