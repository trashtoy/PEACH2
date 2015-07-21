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
 * @since  2.1.0
 */
namespace Peach\Markup;

/**
 * XML の仕様書で定義されている以下の EBNF に基づいて,
 * 要素名や属性名などのバリデーションを行います.
 * 
 * <pre>
 * NameStartChar       ::= ":" | [A-Z] | "_" | [a-z] | [#xC0-#xD6] |
 *                         [#xD8-#xF6] | [#xF8-#x2FF] | [#x370-#x37D] | [#x37F-#x1FFF] | [#x200C-#x200D] |
 *                         [#x2070-#x218F] | [#x2C00-#x2FEF] | [#x3001-#xD7FF] | [#xF900-#xFDCF] | [#xFDF0-#xFFFD] | [#x10000-#xEFFFF]
 * NameChar            ::= NameStartChar | "-" | "." | [0-9] | #xB7 | [#x0300-#x036F] | [#x203F-#x2040]
 * Name                ::= NameStartChar (NameChar)*
 * </pre>
 * 
 * 参考文献: {@link http://www.w3.org/TR/REC-xml/ Extensible Markup Language (XML) 1.0 (Fifth Edition)}
 */
class NameValidator
{
    /**
     * このクラスはインスタンス化できません.
     */
    private function __construct() {}
    
    /**
     * 指定された文字列が XML で定義されている Name のネーミングルールに合致するかどうか調べます.
     * 
     * @param  string $name 検査対象の文字列
     * @return bool         指定された文字列が Name として妥当な場合のみ true
     */
    public static function validate($name)
    {
        if (self::validateFast($name)) {
            return true;
        }
        return false;
    }
    
    /**
     * 使用頻度の高い Name 文字列 (例えば HTML タグなど) について,
     * 簡易な正規表現を使って検査します.
     * 
     * @param  string $name 検査対象の文字列
     * @return bool         "h1", "img" など, ASCII 文字から成る妥当な Name 文字列の場合のみ true
     */
    private static function validateFast($name)
    {
        $firstNameCharClass = "[a-zA-Z_:]";
        $nameCharClass      = "[a-zA-Z0-9_:\\.\\-]";
        $pattern            = "/\\A{$firstNameCharClass}{$nameCharClass}*\\z/";
        return (0 < preg_match($pattern, $name));
    }
}
