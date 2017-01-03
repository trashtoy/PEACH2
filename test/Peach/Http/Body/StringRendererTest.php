<?php
namespace Peach\Http\Body;

class StringRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringRenderer
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = StringRenderer::getInstance();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * getInstance() のテストです. 以下を確認します.
     * 
     * - StringRenderer のインスタンスを返すことを確認します.
     * - 常に同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\Http\Body\StringRenderer::getInstance
     */
    public function testGetInstance()
    {
        $obj = StringRenderer::getInstance();
        $this->assertSame("Peach\\Http\\Body\\StringRenderer", get_class($obj));
        $this->assertSame($this->object, $obj);
    }
    
    /**
     * 引数を文字列に変換した結果を返すことを確認します.
     * 
     * @covers Peach\Http\Body\StringRenderer::render
     */
    public function testRender()
    {
        $obj = $this->object;
        $this->assertSame("hoge", $obj->render("hoge"));
        $this->assertSame("123", $obj->render(123));
    }
}
