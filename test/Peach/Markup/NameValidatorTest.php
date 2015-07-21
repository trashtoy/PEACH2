<?php
namespace Peach\Markup;

class NameValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * validateFast() のテストです. 以下を確認します.
     * 
     * - 1 文字目が半角アルファベットまたは ":", "-" で始まる文字列のみ true を返すこと
     * - 2 文字目以降が半角アルファベット, 数字, ":", "_", ".", "-" から成る文字列のみ true を返すこと
     * 
     * @covers Peach\Markup\NameValidator::validate
     * @covers Peach\Markup\NameValidator::validateFast
     */
    public function testValidateFast()
    {
        $invalid = array("", "1h", "<img>", " p", "input\n");
        $valid   = array("h1", "img", "_foo", ":bar", "this-is.test");
        foreach ($invalid as $name) {
            $this->assertFalse(NameValidator::validate($name));
        }
        foreach ($valid as $name) {
            $this->assertTrue(NameValidator::validate($name));
        }
    }
}
