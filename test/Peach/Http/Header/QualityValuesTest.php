<?php
namespace Peach\Http\Header;

class QualityValuesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QualityValues
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QualityValues("Accept-Language", array("ja" => 1.0, "en-US" => 0.9, "en-GB" => 0.8, "en" => 0.7));
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * 妥当なヘッダー名を引数に指定した場合に正常終了することを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     */
    public function test__constructSuccess()
    {
        new QualityValues("Valid-Name", array("ja" => 1.0, "en-US" => 0.9, "en-GB" => 0.8, "en" => 0.7));
    }
    
    /**
     * 妥当でないヘッダー名を引数に指定した場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidName()
    {
        new QualityValues("Invalid/Name", array("ja" => 1.0, "en-US" => 0.9, "en-GB" => 0.8, "en" => 0.7));
    }
    
    /**
     * qvalue の値が小数でない場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     * @covers Peach\Http\Header\QualityValues::validateQvalue
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidQvalue()
    {
        $q = array("ja" => 1.0, "en-US" => "hoge", "en" => 0.5);
        new QualityValues("Accept-Language", $q);
    }
    
    /**
     * qvalue の値が 0 以上 1 以下でない場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     * @covers Peach\Http\Header\QualityValues::validateQvalue
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidRange()
    {
        $q = array("ja" => 2.0, "en-US" => 0.5, "en" => 0.1);
        new QualityValues("Accept-Language", $q);
    }
    
    /**
     * qvalue の値に不正な文字が含まれていた場合に InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     * @covers Peach\Http\Header\QualityValues::validateQvalue
     * @expectedException \InvalidArgumentException
     */
    public function test__constructFailByInvalidQvalueName()
    {
        $q = array("ja" => 1.0, "foo bar" => 0.5, "baz" => 0.3);
        new QualityValues("Accept-Language", $q);
    }
    
    /**
     * @covers Peach\Http\Header\QualityValues::format
     */
    public function testFormat()
    {
        $obj = $this->object;
        $this->assertSame("ja,en-US;q=0.9,en-GB;q=0.8,en;q=0.7", $obj->format());
    }
    
    /**
     * 配列の定義の順番に関係なく qvalue の値が大きい順に出力されることを確認します.
     * 
     * @covers Peach\Http\Header\QualityValues::__construct
     * @covers Peach\Http\Header\QualityValues::format
     */
    public function testFormatWithSort()
    {
        $q   = array("en" => 0.2, "ja" => 1.0, "en-GB" => 0.5, "en-US" => 0.7);
        $obj = new QualityValues("Accept-Language", $q);
        $this->assertSame("ja,en-US;q=0.7,en-GB;q=0.5,en;q=0.2", $obj->format());
    }
    
    /**
     * @covers Peach\Http\Header\QualityValues::getName
     */
    public function testGetName()
    {
        $obj = $this->object;
        $this->assertSame("Accept-Language", $obj->getName());
    }
}
