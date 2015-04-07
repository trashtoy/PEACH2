<?php
namespace Peach\Markup;
require_once(__DIR__ . "/BuilderTest.php");
require_once(__DIR__ . "/TestUtil.php");

class DebugBuilderTest extends BuilderTest
{
    /**
     * @var DebugBuilder
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DebugBuilder();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * build() のテストです. 以下を確認します.
     * 
     * - ノードの構造をあらわすデバッグ文字列を返すこと
     * - 返り値と同じ文字列が自動的に echo されること
     * 
     * @covers Peach\Markup\DebugBuilder::__construct
     * @covers Peach\Markup\DebugBuilder::build
     * @covers Peach\Markup\DebugBuilder::createContext
     * @covers Peach\Markup\Context::handle
     */
    public function testBuild()
    {
        $builder  = $this->object;
        $node     = TestUtil::getTestNode();
        $expected = TestUtil::getDebugBuildResult();
        $this->expectOutputString($expected);
        $this->assertSame($expected, $builder->build($node));
    }
    
    /**
     * コンストラクタ引数に false を指定して DebugBuilder を初期化した場合,
     * 自動 echo がされないことを確認します.
     * 
     * @covers Peach\Markup\DebugBuilder::__construct
     * @covers Peach\Markup\DebugBuilder::build
     * @covers Peach\Markup\DebugBuilder::createContext
     * @covers Peach\Markup\Context::handle
     */
    public function testBuildNotEcho()
    {
        $builder  = new DebugBuilder(false);
        $node     = TestUtil::getTestNode();
        $expected = TestUtil::getDebugBuildResult();
        $this->expectOutputString("");
        $this->assertSame($expected, $builder->build($node));
    }
}
