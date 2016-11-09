<?php
namespace Peach\Markup;

use PHPUnit_Framework_TestCase;

abstract class AbstractHelperTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * コンストラクタで指定した Helper と同一のオブジェクトを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::getParentHelper
     */
    abstract public function testGetParentHelper();
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::createElement
     */
    abstract public function testCreateElement();
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::tag
     */
    abstract public function testTag();
    
    /**
     * このオブジェクトのベースの Helper の実行結果と同じものを返すことを確認します.
     * 
     * @covers Peach\Markup\AbstractHelper::write
     */
    abstract public function testWrite();
}
