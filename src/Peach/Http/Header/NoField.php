<?php

namespace Peach\Http\Header;
use Peach\Http\HeaderField;

class NoField implements HeaderField
{
    private function __construct() {}
    
    /**
     * 
     * @return string
     */
    public function format()
    {
        return "";
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return "";
    }
    
    public function getValue()
    {
        return null;
    }
    
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}
