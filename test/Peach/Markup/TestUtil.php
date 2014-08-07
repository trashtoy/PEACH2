<?php
namespace Peach\Markup;

class TestUtil
{
    private function __construct() {}
    
    /**
     * @return ContainerElement
     */
    public static function getTestNode()
    {
        static $html = null;
        if ($html === null) {
            $html = new ContainerElement("html");
            $html->setAttribute("lang", "ja");
            $html->append(self::createHead());
            $html->append(self::createBody());
        }
        return $html;
    }
    
    /**
     * @return ContainerElement
     */
    private static function createHead()
    {
        $meta   = new EmptyElement("meta");
        $meta->setAttributes(array("http-equiv" => "Content-Type", "content" => "text/html; charset=UTF-8"));
        $title  = new ContainerElement("title");
        $title->append("TEST PAGE");
        
        $head   = new ContainerElement("head");
        $head->append($meta);
        $head->append($title);
        return $head;
    }
    
    /**
     * @return ContainerElement
     */
    private static function createBody()
    {
        $body   = new ContainerElement("body");
        $body->append(self::createForm());
        return $body;
    }
    
    /**
     * @return ContainerElement
     */
    private static function createForm()
    {
        $text   = new EmptyElement("input");
        $text->setAttributes(array("type" => "text", "name" => "param1", "value" => ""));
        $br     = new EmptyElement("br");
        $check  = new EmptyElement("input");
        $check->setAttributes(array("type" => "checkbox", "name" => "flag1", "value" => "1"));
        $check->setAttribute("checked");
        $submit = new EmptyElement("input");
        $submit->setAttributes(array("type" => "submit", "name" => "submit", "value" => "Send"));
        
        $form   = new ContainerElement("form");
        $form->setAttributes(array("method" => "post", "action" => "sample.php"));
        $form->append("Name");
        $form->append($text);
        $form->append($br);
        $form->append($check);
        $form->append("Enable something");
        $form->append($br);
        $form->append($submit);
        return $form;
    }
    
    /**
     * テストノードをデフォルトの条件でマークアップした場合の想定結果を返します.
     * デフォルトの条件は以下の通りです.
     * - 文書型: XHTML
     * - インデント: 4 スペース
     * - 改行コード: CRLF
     * 
     * @return string
     */
    public static function getDefaultBuildResult()
    {
        static $expected = null;
        if ($expected === null) {
            $expected = implode("\r\n", array(
                '<html lang="ja">',
                '    <head>',
                '        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
                '        <title>TEST PAGE</title>',
                '    </head>',
                '    <body>',
                '        <form method="post" action="sample.php">',
                '            Name',
                '            <input type="text" name="param1" value="" />',
                '            <br />',
                '            <input type="checkbox" name="flag1" value="1" checked="checked" />',
                '            Enable something',
                '            <br />',
                '            <input type="submit" name="submit" value="Send" />',
                '        </form>',
                '    </body>',
                '</html>',
            ));
        }
        return $expected;
    }
    
    /**
     * 条件をカスタマイズした状態における, テストノードのマークアップ結果を返します.
     * 条件は以下の通りです.
     * - 文書型: HTML
     * - インデント: 2 スペース
     * - 改行コード: LF
     * 
     * @return string
     */
    public static function getCustomBuildResult()
    {
        static $expected = null;
        if ($expected === null) {
            $expected = implode("\n", array(
                '<html lang="ja">',
                '  <head>',
                '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">',
                '    <title>TEST PAGE</title>',
                '  </head>',
                '  <body>',
                '    <form method="post" action="sample.php">',
                '      Name',
                '      <input type="text" name="param1" value="">',
                '      <br>',
                '      <input type="checkbox" name="flag1" value="1" checked>',
                '      Enable something',
                '      <br>',
                '      <input type="submit" name="submit" value="Send">',
                '    </form>',
                '  </body>',
                '</html>',
            ));
        }
        return $expected;
    }
}
