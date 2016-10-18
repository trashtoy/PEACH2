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
 * HTTP における HTTP メッセージの送受信を担当するインタフェースです.
 * 
 * クライアント (WEBブラウザなど) から受け取った情報を {@link Request} オブジェクトとして取り出したり,
 * {@link Response} オブジェクトを HTTP レスポンスとしてクライアントに送信したりする機能を持ちます.
 */
interface Endpoint
{
    /**
     * この Endpoint の処理対象の Request オブジェクトを返します.
     * 
     * @return Request
     * @todo   Request クラスを実装する
     */
    public function getRequest();
    
    /**
     * 引数の Response オブジェクトをクライアントに送信します.
     * 
     * @param Response $response 送信対象の Response
     */
    public function send(Response $response);
}
