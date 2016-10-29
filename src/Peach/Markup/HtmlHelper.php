<?php
/*
 * Copyright (c) 2016 @trashtoy
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
namespace Peach\Markup;

use InvalidArgumentException;

class HtmlHelper extends AbstractHelper
{

    /**
     * @var string
     */
    const MODE_HTML4_STRICT        = "html4s";
    
    /**
     * @var string
     */
    const MODE_HTML4_TRANSITIONAL  = "html4t";
    
    /**
     * @var string
     */
    const MODE_XHTML1_STRICT       = "xhtml1s";
    
    /**
     * @var string
     */
    const MODE_XHTML1_TRANSITIONAL = "xhtml1t";
    
    /**
     * @var string
     */
    const MODE_XHTML1_1            = "xhtml1_1";
    
    /**
     * @var string
     */
    const MODE_HTML5               = "html5";
    
    /**
     *
     * @var string
     */
    private $docType;
    
    /**
     *
     * @var bool
     */
    private $isXhtml;
    
    public function __construct(Helper $parent, $docType = null, $isXhtml = null)
    {
        parent::__construct($parent);
        $this->docType = $docType;
        $this->isXhtml = $isXhtml;
    }
    
    /**
     * 
     * @param  string $mode
     * @return HtmlHelper
     */
    public static function newInstance($mode = null)
    {
        $actualMode     = self::detectMode($mode);
        $baseHelper     = self::createBaseHelper($actualMode);
        $docType        = self::getDocTypeFromMode($actualMode);
        $isXhtml        = self::checkXhtmlFromMode($actualMode);
        return new self($baseHelper, $docType, $isXhtml);
    }
    
    /**
     * 
     * @param  string $mode
     * @return HtmlHelper
     */
    private static function createBaseHelper($mode)
    {
        $isXhtml        = self::checkXhtmlFromMode($mode);
        $builder        = self::createBuilder($isXhtml);
        $emptyNodeNames = self::getEmptyNodeNames();
        return new BaseHelper($builder, $emptyNodeNames);
    }
    
    /**
     * 
     * @return Component
     */
    public function xmlDec()
    {
        return $this->isXhtml ? new Code('<?xml version="1.0" encoding="UTF-8"?>') : None::getInstance();
    }
    
    /**
     * 
     * @return Component
     */
    public function docType()
    {
        return strlen($this->docType) ? new Code($this->docType) : None::getInstance();
    }
    
    private static function detectMode($param)
    {
        static $mapping = null;
        if ($mapping === null) {
            $keys = array(
                self::MODE_HTML4_TRANSITIONAL,
                self::MODE_HTML4_STRICT,
                self::MODE_XHTML1_STRICT,
                self::MODE_XHTML1_TRANSITIONAL,
                self::MODE_XHTML1_1,
                self::MODE_HTML5,
            );
            $altMapping = array(
                ""      => self::MODE_HTML5,
                "html"  => self::MODE_HTML5,
                "html4" => self::MODE_HTML4_TRANSITIONAL,
                "xhtml" => self::MODE_XHTML1_TRANSITIONAL,
            );
            $mapping = array_merge(array_combine($keys, $keys), $altMapping);
        }
        
        $key = strtolower($param);
        if (array_key_exists($key, $mapping)) {
            return $mapping[$key];
        } else {
            throw new InvalidArgumentException("Invalid mode name: {$param}");
        }
    }
    
    /**
     * 
     * @param  string $mode
     * @return bool
     */
    private static function checkXhtmlFromMode($mode)
    {
        static $xhtmlModes = array(
            self::MODE_XHTML1_STRICT,
            self::MODE_XHTML1_TRANSITIONAL,
            self::MODE_XHTML1_1,
        );
        return in_array($mode, $xhtmlModes);
    }
    
    private static function getDocTypeFromMode($mode)
    {
        static $docTypeList = array(
            self::MODE_HTML4_STRICT        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
            self::MODE_HTML4_TRANSITIONAL  => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
            self::MODE_XHTML1_STRICT       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            self::MODE_XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            self::MODE_XHTML1_1            => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            self::MODE_HTML5               => '<!DOCTYPE html>',
        );
        return $docTypeList[$mode];
    }
    
    /**
     * 新しい DefaultBuilder を生成します.
     * @param  bool $isXhtml XHTML 形式の場合は true, HTML 形式の場合は false
     * @return DefaultBuilder
     */
    private static function createBuilder($isXhtml = false)
    {
        // @codeCoverageIgnoreStart
        static $breakControl = null;
        if (!isset($breakControl)) {
            $breakControl = new NameBreakControl(
                array("html", "head", "body", "ul", "ol", "dl", "table"),
                array("pre", "code", "textarea")
            );
        }
        // @codeCoverageIgnoreEnd
        
        $renderer = $isXhtml ? XmlRenderer::getInstance() : SgmlRenderer::getInstance();
        $builder  = new DefaultBuilder();
        $builder->setBreakControl($breakControl);
        $builder->setRenderer($renderer);
        return $builder;
    }
    
    /**
     * HTML4, XHTML1, HTML5 にて定義されている空要素タグまたは Void elements
     * の一覧を返します.
     * 返り値の配列は, 以下に挙げる要素名をマージしたものとなります.
     * 
     * Empty elements by XHTML1 (https://www.w3.org/TR/xhtml1/)
     *     - area, base, basefont, br, col, frame, hr, img, input, isindex, link, meta, param
     * Void elements by HTML5 (https://www.w3.org/TR/html5/syntax.html#void-elements)
     *     - area, base, br, col, embed, hr, img, input, keygen, link, meta, param, source, track, wbr
     * 
     * @return array
     * @ignore
     */
    private static function getEmptyNodeNames()
    {
        static $emptyList = null;
        if ($emptyList === null) {
            $emptyList = array("area", "base", "basefont", "br", "col", "embed", "frame", "hr", "img", "input", "isindex", "keygen", "link", "meta", "param", "source", "track", "wbr");
        }
        return $emptyList;
    }
}
