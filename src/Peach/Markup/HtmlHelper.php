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

class HtmlHelper extends AbstractHelper
{

    /**
     * 
     * @param  bool $isXhtml
     * @return HtmlHelper
     */
    public static function newInstance($isXhtml = false)
    {
        $builder        = self::createBuilder($isXhtml);
        $emptyNodeNames = self::getEmptyNodeNames();
        return new self(new BaseHelper($builder, $emptyNodeNames));
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
