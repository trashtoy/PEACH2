<?php
namespace Peach\Markup;

/**
 * 各 Component の accept() のテストを行うための Context です.
 */
class TestContext extends Context
{
    /**
     * @var string
     */
    private $result;
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function handleEmptyElement(EmptyElement $node)
    {
        $this->result = "handleEmptyElement";
    }
    
    public function handleText(Text $node)
    {
        $this->result = "handleText";
    }
    
    public function handleCode(Code $node)
    {
        $this->result = "handleCode";
    }
    
    public function handleNone(None $none)
    {
        $this->result = "handleNone";
    }
}
