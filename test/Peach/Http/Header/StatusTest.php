<?php
namespace Peach\Http\Header;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Status
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Status("404", "Not Found");
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタの第 1 引数が空文字列として評価された場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::cleanCode
     * @expectedException InvalidArgumentException
     */
    public function test__constructFailByEmptyCode()
    {
        new Status("");
    }
    
    /**
     * 第 1 引数が空文字列として評価された場合に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::cleanCode
     * @expectedException InvalidArgumentException
     */
    public function test__constructFailByInvalidCode()
    {
        new Status("asdf");
    }
    
    /**
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::getCode
     */
    public function testGetCode()
    {
        $obj = $this->object;
        $this->assertSame("404", $obj->getCode());
    }
    
    /**
     * @covers Peach\Http\Header\Status::__construct
     * @covers Peach\Http\Header\Status::getReasonPhrase
     */
    public function testGetReasonPhrase()
    {
        $obj = $this->object;
        $this->assertSame("Not Found", $obj->getReasonPhrase());
    }
    
    /**
     * @covers Peach\Http\Header\Status::format
     */
    public function testFormat()
    {
        $obj = $this->object;
        $this->assertSame("404", $obj->format());
    }
    
    /**
     * 文字列 ":status" を返すことを確認します.
     * 
     * @covers Peach\Http\Header\Status::getName
     */
    public function testGetName()
    {
        $obj = $this->object;
        $this->assertSame(":status", $obj->getName());
    }
    
    /**
     * ステータスコードと Reason-Phrase からなる配列を返すことを確認します.
     * 
     * @covers Peach\Http\Header\Status::getValue
     */
    public function testGetValue()
    {
        $obj      = $this->object;
        $expected = array("404", "Not Found");
        $this->assertSame($expected, $obj->getValue());
    }

    /**
     * getOK() のテストです. 以下を確認します.
     * 
     * - ステータスコード "200" および Phrase-Reason "OK" から成る Status オブジェクトを返すこと
     * - 複数回実行した際に同一のオブジェクトを返すこと
     * 
     * @covers Peach\Http\Header\Status::getOK
     */
    public function testGetOK()
    {
        $obj1      = Status::getOK();
        $expected1 = array("200", "OK");
        $this->assertSame($expected1, $obj1->getValue());
        
        $obj2      = Status::getOK();
        $this->assertSame($obj1, $obj2);
    }
}
