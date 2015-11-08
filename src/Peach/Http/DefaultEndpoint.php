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
     * @return Request
     * @todo 実装する
     */
    public function getRequest()
    {
        
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