<?php
namespace Peach\Markup;
require_once(__DIR__ . "/TestContext.php");

class NoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var None
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = None::getInstance();
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
     * - None のインスタンスを返すことを確認します.
     * - 常に同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\Markup\None::getInstance
     */
    public function testGetInstance()
    {
        $obj = None::getInstance();
        $this->assertSame("Peach\\Markup\\None", get_class($obj));
        $this->assertSame($this->object, $obj);
    }
    
    /**
     * Context の handleNone() が呼び出されることを確認します.
     * 
     * @covers Peach\Markup\None::accept
     */
    public function testAccept()
    {
        $context = new TestContext();
        $this->object->accept($context);
        $this->assertSame("handleNone", $context->getResult());
    }
}
