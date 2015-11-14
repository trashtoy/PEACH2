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

use Peach\Http\Header\Raw;
use Peach\Util\Strings;

/**
 * 一般的な WEB アプリケーションで使用されることを想定した Endpoint です.
 * 
 * このクラスは $_GET, $_POST, $_SERVER などの情報を基にして Request を構築します.
 * send() メソッドでは Response の内容を
 * {@link http://php.net/manual/function.header.php header()} および
 * {@link http://php.net/manual/function.echo.php echo()} を利用してクライアントに送信します.
 */
class DefaultEndpoint implements Endpoint
{
    /**
     * 
     * @var Request
     */
    private $request;
    
    public function __construct()
    {
    }
    
    /**
     * @return Request
     */
    private function createRequest()
    {
        $keys    = array_keys($_SERVER);
        $request = new Request();
        foreach ($keys as $key) {
            if (!Strings::startsWith($key, "HTTP_")) {
                continue;
            }
            $name  = str_replace("_", "-", substr($key, 5));
            $value = $_SERVER[$key];
            $request->setHeader(new Raw($name, $value)); // @todo ヘッダーの内容に応じて生成するオブジェクトの型を変える
        }
        
        $scheme = isset($_SERVER["HTTPS"]) ? "https" : "http";
        $request->setQuery($_GET);
        $request->setPost($_POST);
        $request->setHeader(new Raw(":path", $_SERVER["REQUEST_URI"]));
        $request->setHeader(new Raw(":scheme", $scheme));
        
        return $request;
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = $this->createRequest();
        }
        return $this->request;
    }
    
    /**
     * 
     * @param Response $response
     * @todo 実装する
     */
    public function send(Response $response)
    {
        
    }
}