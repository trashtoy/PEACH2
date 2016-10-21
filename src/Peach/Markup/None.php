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
namespace Peach\Markup;

/**
 * 「何もない」ことをあらわす Component です. (Null Object パターン)
 *
 * この Component は以下の特徴を持ちます.
 * 
 * - コンテナに append しても何も追加されない
 * - Context に処理させても何も行われない (ただし DebugContext を除く)
 */
class None implements Component
{
    /**
     * このクラスはインスタンス化できません.
     */
    private function __construct() {}
    
    /**
     * このクラスの唯一のインスタンスを返します.
     * @return None
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        
        return $instance;
    }
    
    /**
     * 指定された Context にこのノードを処理させます.
     * {@link Context::handleNone()} を呼び出します.
     * @param Context $context
     */
    public function accept(Context $context)
    {
        $context->handleNone($this);
    }
    
    /**
     * 空の NodeList を返します.
     * 
     * @return Component 空の NodeList
     */
    public function getAppendee()
    {
        return new NodeList();
    }
}
