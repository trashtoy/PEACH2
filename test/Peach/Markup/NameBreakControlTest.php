<?php
namespace Peach\Markup;

class NameBreakControlTest extends \PHPUnit_Framework_TestCase
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
     * break() のテストです. 以下を確認します.
     * 
     * - 強制改行する要素の場合, オリジナルの条件を無視して改行します
     * - 強制改行しない要素の場合, オリジナルの条件を無視して改行しないようにします
     * - どちらにも属する要素の場合, 強制改行のほうが優先されます.
     * - どちらにも属さない要素の場合, オリジナルの改行ルールに従います
     * 
     * @covers Peach\Markup\NameBreakControl::__construct
     * @covers Peach\Markup\NameBreakControl::breaks
     */
    public function testBreaks()
    {
        $c = new NameBreakControl(array("A", "B", "C", "Q"), array("Q", "X", "Y", "Z"));
        
        $this->assertTrue($c->breaks($this->createBreakNode("A")));
        $this->assertTrue($c->breaks($this->createNoBreakNode("B")));
        
        $this->assertFalse($c->breaks($this->createBreakNode("X")));
        $this->assertFalse($c->breaks($this->createNoBreakNode("Y")));
        
        $this->assertTrue($c->breaks($this->createBreakNode("Q")));
        $this->assertTrue($c->breaks($this->createNoBreakNode("Q")));
        
        $this->assertTrue($c->breaks($this->createBreakNode("T")));
        $this->assertFalse($c->breaks($this->createNoBreakNode("T")));
    }
    
    /**
     * @param  string $name
     * @return ContainerElement
     */
    private function createBreakNode($name)
    {
        $test = new ContainerElement($name);
        $test->appendNode("First Child");
        $test->appendNode("Second Child");
        return $test;
    }
    
    /**
     * @param  string $name
     * @return ContainerElement
     */
    private function createNoBreakNode($name)
    {
        $test = new ContainerElement($name);
        $test->appendNode("First Child");
        return $test;
    }
}
