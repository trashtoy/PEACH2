<?php

namespace Peach\Http\Header;

use Peach\Http\HeaderField;

/**
 * 存在しない HeaderField をあらわします. (Null Object パターン)
 * 
 * この HeaderField の getName() および format() は空文字列を返します..
 */
class NoField implements HeaderField
{
    /**
     * このクラスを直接インスタンス化することはできません.
     */
    private function __construct() {}
    
    /**
     * 空文字列を返します.
     * 
     * @return string 空文字列
     */
    public function format()
    {
        return "";
    }
    
    /**
     * 空文字列を返します.
     * 
     * @return string 空文字列
     */
    public function getName()
    {
        return "";
    }
    
    /**
     * このフィールドは値を持たないため null を返します.
     * @return null
     */
    public function getValue()
    {
        return null;
    }
    
    /**
     * このクラスの唯一のインスタンスを返します.
     * @return NoField 唯一の NoField インスタンス
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

}
