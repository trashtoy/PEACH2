<?php
namespace Peach\Http\Header;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Peach\DT\Timestamp;

class CookieOptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CookieOptions
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CookieOptions();
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * setExpires() でセットした Timestamp が getExpires() から取得できることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::getExpires
     * @covers Peach\Http\Header\CookieOptions::setExpires
     */
    public function testAccessExpires()
    {
        $obj       = $this->object;
        $timestamp = new Timestamp(2012, 5, 21, 7, 34, 45);
        $obj->setExpires($timestamp);
        $this->assertSame($timestamp, $obj->getExpires());
    }
    
    /**
     * setExpires() を一度も実行していない場合は null を返すことを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::getExpires
     */
    public function testGetExpiresByDefault()
    {
        $obj = new CookieOptions();
        $this->assertNull($obj->getExpires());
    }
    
    /**
     * setTimeZoneOffset() でセットした値が getTimeZoneOffset() から取得できることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::getTimeZoneOffset
     * @covers Peach\Http\Header\CookieOptions::setTimeZoneOffset
     */
    public function testAccessTimeZoneOffset()
    {
        $obj = $this->object;
        $obj->setTimeZoneOffset(-540);
        $this->assertSame(-540, $obj->getTimeZoneOffset());
    }
    
    /**
     * setTimeZoneOffset() を一度も実行していない場合は null を返すことを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::getTimeZoneOffset
     */
    public function testGetTimeZoneOffsetByDefault()
    {
        $obj = new CookieOptions();
        $this->assertNull($obj->getTimeZoneOffset());
    }
    
    /**
     * setTimeZoneOffset() で指定した値が -1425 以上 1425 以下に丸められることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setTimeZoneOffset
     */
    public function testSetTimeZoneOffsetByInvalidValue()
    {
        $obj = $this->object;
        $obj->setTimeZoneOffset(-2000);
        $this->assertSame(-1425, $obj->getTimeZoneOffset());
        $obj->setTimeZoneOffset(3000);
        $this->assertSame(1425, $obj->getTimeZoneOffset());
    }
    
    /**
     * formatOptions() のテストです. 以下を確認します.
     * 
     * - デフォルトの状態では空の配列を返すこと
     * - セットされた属性の値が返り値の配列内に含まれていること
     * 
     * @covers Peach\Http\Header\CookieOptions::formatOptions
     */
    public function testFormatOptions()
    {
        $obj = $this->object;
        $this->assertSame(array(), $obj->formatOptions());
        
        $expected = array(
            "expires=Fri, 13-Feb-2009 23:31:30 GMT",
            "max-age=3600",
            "domain=example.com",
            "path=/foo/bar",
            "secure",
            "httponly",
        );
        $obj->setTimeZoneOffset(-540);
        $obj->setExpires(new Timestamp(2009, 2, 14, 8, 31, 30));
        $obj->setMaxAge(3600);
        $obj->setDomain("example.com");
        $obj->setPath("/foo/bar");
        $obj->setSecure(true);
        $obj->setHttpOnly(true);
        $this->assertSame($expected, $obj->formatOptions());
    }
    
    /**
     * expires 属性が書式化されることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::formatExpires
     * @covers Peach\Http\Header\CookieOptions::formatOptions
     */
    public function testFormatExpires()
    {
        $expected = array("expires=Sun, 20-May-2012 22:34:45 GMT");
        $obj      = $this->object;
        $obj->setExpires(new Timestamp(2012, 5, 21, 7, 34, 45));
        $obj->setTimeZoneOffset(-540);
        $this->assertSame($expected, $obj->formatOptions());
    }
    
    /**
     * setMaxAge() および getMaxAge() のテストです. 以下を確認します.
     * 
     * - setMaxAge() でセットした値が getMaxAge() から取得できること
     * - デフォルトでは null を返すこと
     * - 整数以外の値をセットした場合は整数に変換されること
     * - 0 未満の値をセットした場合は 0 に変換されること
     * - 一度セットした値を null で初期化できること
     * 
     * @covers Peach\Http\Header\CookieOptions::getMaxAge
     * @covers Peach\Http\Header\CookieOptions::setMaxAge
     */
    public function testAccessMaxAge()
    {
        $obj1 = new CookieOptions();
        $obj1->setMaxAge(3600);
        $this->assertSame(3600, $obj1->getMaxAge());
        
        $obj2 = new CookieOptions();
        $this->assertNull($obj2->getMaxAge());
        
        $obj3 = new CookieOptions();
        $obj3->setMaxAge("1234.56789");
        $this->assertSame(1234, $obj3->getMaxAge());
        
        $obj4 = new CookieOptions();
        $obj4->setMaxAge(-1800);
        $this->assertSame(0, $obj4->getMaxAge());
        
        $obj5 = new CookieOptions();
        $obj5->setMaxAge(1800);
        $this->assertNotNull($obj5->getMaxAge());
        $obj5->setMaxAge(null);
        $this->assertNull($obj5->getMaxAge());
    }
    
    /**
     * setDomain() および getDomain() のテストです. 以下を確認します.
     * 
     * - setDomain() でセットした値が getDomain() から取得できること
     * - デフォルトでは null を返すこと
     * - 一度セットした値を null で初期化できること
     * 
     * @covers Peach\Http\Header\CookieOptions::getDomain
     * @covers Peach\Http\Header\CookieOptions::setDomain
     */
    public function testAccessDomain()
    {
        $obj1 = new CookieOptions();
        $obj1->setDomain("example.com");
        $this->assertSame("example.com", $obj1->getDomain());
        
        $obj2 = new CookieOptions();
        $this->assertNull($obj2->getDomain());
        
        $obj3 = new CookieOptions();
        $obj3->setDomain("example.org");
        $this->assertNotNull($obj3->getDomain());
        $obj3->setDomain(null);
        $this->assertNull($obj3->getDomain());
    }
    
    /**
     * 不正なドメイン名を指定して setDomain() を実行した際に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setDomain
     * @covers Peach\Http\Header\CookieOptions::validateDomain
     */
    public function testValidateDomainFail()
    {
        $obj1   = new CookieOptions();
        $ngList = array(
            "example..com",  // empty label
            "-example.com",  // invalid head hyphen
            "e{xampl}e.com", // invalid characters
        );
        foreach ($ngList as $domain) {
            try {
                $obj1->setDomain($domain);
                $this->fail("String '{$domain}' must be treated as invalid");
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    /**
     * 妥当なログイン名 (ただし実在しうる不正なログイン名も含みます)
     * を setDomain() の引数にセットした際に正常終了することを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setDomain
     * @covers Peach\Http\Header\CookieOptions::validateDomain
     */
    public function testValidateDomainSuccess()
    {
        $obj1   = new CookieOptions();
        $okList = array(
            "xn--28j2af.xn--q9jyb4c",
            "1101.org",
            "localhost",
            null,
        );
        foreach ($okList as $domain) {
            $obj1->setDomain($domain);
        }
    }
    
    /**
     * setPath() および getPath() のテストです. 以下を確認します.
     * 
     * - setPath() でセットした値が getPath() から取得できること
     * - デフォルトでは null を返すこと
     * - 一度セットした値を null で初期化できること
     * 
     * @covers Peach\Http\Header\CookieOptions::getPath
     * @covers Peach\Http\Header\CookieOptions::setPath
     */
    public function testAccessPath()
    {
        $obj1 = new CookieOptions();
        $obj1->setPath("/foo/bar");
        $this->assertSame("/foo/bar", $obj1->getPath());
        
        $obj2 = new CookieOptions();
        $this->assertNull($obj2->getPath());
        
        $obj3 = new CookieOptions();
        $obj3->setPath("/hoge/fuga/");
        $this->assertNotNull($obj3->getPath());
        $obj3->setPath(null);
        $this->assertNull($obj3->getPath());
    }
    
    /**
     * 不正なパスを指定して setPath() を実行した際に
     * InvalidArgumentException をスローすることを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setPath
     * @covers Peach\Http\Header\CookieOptions::validatePath
     */
    public function testValidatePathFail()
    {
        $obj1   = new CookieOptions();
        $ngList = array(
            "//foo/bar",  // empty first segment
            "/foo<bar>/", // invalid char
            "foo/bar",    // not absolute path
            "/foo/%1xyz", // invalid percent encoding
        );
        foreach ($ngList as $path) {
            try {
                $obj1->setPath($path);
                $this->fail("String '{$path}' must be treated as invalid");
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    /**
     * 妥当なパスを setPath() の引数にセットした際に正常終了することを確認します.
     * 
     * @covers Peach\Http\Header\CookieOptions::setPath
     * @covers Peach\Http\Header\CookieOptions::validatePath
     */
    public function testValidatePathSuccess()
    {
        $obj1   = new CookieOptions();
        $okList = array(
            "/",
            "/foo//bar///baz////",
            "/foo/My%20Documents/test\$01.txt",
            null,
        );
        foreach ($okList as $domain) {
            $obj1->setPath($domain);
        }
    }
    
    /**
     * setSecure() および hasSecure() のテストです. 以下を確認します.
     * 
     * - setSecure() でセットした値が hasSecure() から取得できること
     * - デフォルトでは hasSecure() が false を返すこと
     * - bool 以外の値をセットした後 hasSecure() が bool 値を返すこと
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::hasSecure
     * @covers Peach\Http\Header\CookieOptions::setSecure
     */
    public function testAccessSecure()
    {
        $obj1 = new CookieOptions();
        $obj1->setSecure(true);
        $this->assertTrue($obj1->hasSecure());
        $obj1->setSecure(false);
        $this->assertFalse($obj1->hasSecure());
        
        $obj2 = new CookieOptions();
        $this->assertFalse($obj2->hasSecure());
        
        $obj3 = new CookieOptions();
        $obj3->setSecure("asdf");
        $this->assertSame(true, $obj3->hasSecure());
        $obj3->setSecure(0);
        $this->assertSame(false, $obj3->hasSecure());
    }
    
    /**
     * setHttpOnly() および hasHttpOnly() のテストです. 以下を確認します.
     * 
     * - setHttpOnly() でセットした値が hasHttpOnly() から取得できること
     * - デフォルトでは hasHttpOnly() が false を返すこと
     * - bool 以外の値をセットした後 hasHttpOnly() が bool 値を返すこと
     * 
     * @covers Peach\Http\Header\CookieOptions::__construct
     * @covers Peach\Http\Header\CookieOptions::hasHttpOnly
     * @covers Peach\Http\Header\CookieOptions::setHttpOnly
     */
    public function testAccessHttpOnly()
    {
        $obj1 = new CookieOptions();
        $obj1->setHttpOnly(true);
        $this->assertTrue($obj1->hasHttpOnly());
        $obj1->setHttpOnly(false);
        $this->assertFalse($obj1->hasHttpOnly());
        
        $obj2 = new CookieOptions();
        $this->assertFalse($obj2->hasHttpOnly());
        
        $obj3 = new CookieOptions();
        $obj3->setHttpOnly("asdf");
        $this->assertSame(true, $obj3->hasHttpOnly());
        $obj3->setHttpOnly(0);
        $this->assertSame(false, $obj3->hasHttpOnly());
    }
}
