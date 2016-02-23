<?php
/*
 * Copyright (c) 2016 @trashtoy
 * https://github.com/trashtoy/
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * PHP class file.
 * @auhtor trashtoy
 * @since  2.2.0
 */
namespace Peach\Http\Header;

use InvalidArgumentException;
use Peach\DT\Timestamp;
use Peach\DT\Util;
use Peach\Util\Values;

/**
 * Set-Cookie ヘッダーの各種属性をあらわすクラスです.
 * このクラスのオブジェクトに対する変更は, 関連するすべての CookieItem オブジェクトに影響を与えます.
 */
class CookieOptions
{
    /**
     * expires 属性の時刻をあらわす Timestamp オブジェクトです.
     * 
     * @var Timestamp
     */
    private $expires;
    
    /**
     * このプログラムが扱う時刻のタイムゾーンです.
     * この値が null の場合はデフォルトのタイムゾーンを適用します.
     * 
     * @var int
     */
    private $timeZoneOffset;
    
    /**
     * max-age 属性をあらわす整数です.
     * @var int
     */
    private $maxAge;
    
    /**
     * domain 属性をあらわす文字列です.
     * 
     * @var string
     */
    private $domain;
    
    /**
     * 属性を何も持たない, 新しい CookieOptions オブジェクトを構築します.
     */
    public function __construct()
    {
    }
    
    /**
     * expires 属性の時刻を設定します.
     * 引数に null を設定した場合は expires 属性を削除します.
     * 
     * @param Timestamp $expires Set-Cookie の expires 属性として表現される時刻
     */
    public function setExpires(Timestamp $expires = null)
    {
        $this->expires = $expires;
    }
    
    /**
     * expires 属性の時刻を返します.
     * expires 属性が設定されていない場合は null を返します.
     * 
     * @return Timestamp expires 属性の時刻
     */
    public function getExpires()
    {
        return $this->expires;
    }
    
    /**
     * このオブジェクトが取り扱う Timestamp オブジェクトの時差を分単位でセットします.
     * このメソッドは expires 属性の出力に影響します.
     * PHP の date.timezone 設定がシステムの時差と異なる場合に使用してください.
     * 通常はこのメソッドを使用する必要はありません.
     * 
     * もしも引数に -23:45 (1425) 以上または +23:45 (-1425) 未満の値を指定した場合は
     * -23:45 または +23:45 に丸めた結果を返します.
     * 
     * @param int $offset 時差
     * @see   Util::cleanTimeZoneOffset()
     */
    public function setTimeZoneOffset($offset)
    {
        $this->timeZoneOffset = ($offset === null) ? null : Util::cleanTimeZoneOffset($offset);
    }
    
    /**
     * このオブジェクトが取り扱う Timestamp オブジェクトの時差を返します.
     * このメソッドはデフォルトで null を返します.
     * 
     * @return int 時差. ただしデフォルトの場合は null
     */
    public function getTimeZoneOffset()
    {
        return $this->timeZoneOffset;
    }
    
    /**
     * max-age 属性の値をセットします.
     * 引数に 0 をセットした場合は対象の Cookie がブラウザから削除されます.
     * 引数が 0 未満の値の場合は 0 として扱われます.
     * 
     * @param int $maxAge max-age 属性の値
     */
    public function setMaxAge($maxAge)
    {
        $this->maxAge = ($maxAge === null) ? null : Values::intValue($maxAge, 0);
    }
    
    /**
     * max-age 属性の値を返します.
     * もしも max-age 属性がセットされていない場合は null を返します.
     * 
     * @return int max-age 属性の値. セットされていない場合は null
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }
    
    /**
     * domain 属性の値をセットします.
     * 引数に null をセットした場合は domain 属性を削除します.
     * 
     * @param string $domain domain 属性の値
     */
    public function setDomain($domain)
    {
        if (!$this->validateDomain($domain)) {
            throw new InvalidArgumentException("Invalid domain: '{$domain}'");
        }
        $this->domain = $domain;
    }
    
    /**
     * 指定された文字列が, ドメイン名として妥当かどうかを確認します.
     * RFC 1035 に基づいて, 引数の文字列が以下の BNF 記法を満たすかどうかを調べます.
     * 妥当な場合は true, そうでない場合は false を返します.
     * 
     * ただし, 本来は Invalid にも関わらず実際に使われているドメイン名に対応するため
     * label の先頭の数字文字列を敢えて許す実装となっています.
     * 
     * <pre>
     * {domain} ::= {subdomain} | " "
     * {subdomain} ::= {label} | {subdomain} "." {label}
     * {label} ::= {letter} [ [ {ldh-str} ] {let-dig} ]
     * {ldh-str} ::= {let-dig-hyp} | {let-dig-hyp} {ldh-str}
     * {let-dig-hyp} ::= {let-dig} | "-"
     * {let-dig} ::= {letter} | {digit}
     * </pre>
     * 
     * @param  string $domain 検査対象のドメイン名
     * @return bool           引数がドメイン名として妥当な場合のみ true
     */
    private function validateDomain($domain)
    {
        if ($domain === null) {
            return true;
        }
        $letter    = "[a-zA-Z0-9]";
        $letDigHyp = "(-|{$letter})";
        $label     = "{$letter}({$letDigHyp}*{$letter})*";
        $pattern   = "{$label}(\\.{$label})*";
        return preg_match("/\\A{$pattern}\\z/", $domain);
    }
    
    /**
     * domain 属性の値を返します.
     * domain 属性がセットされていない場合は null を返します.
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    /**
     * このオブジェクトが持つ各属性を書式化し, 結果を配列で返します.
     * 
     * @return array 各属性を書式化した結果の配列
     * @ignore
     * @todo 複数の Set-Cookie ヘッダーで同じオプションを適用することを想定し, 返り値をキャッシュできるようにする
     */
    public function formatOptions()
    {
        $result = array();
        if ($this->expires !== null) {
            $result[] = $this->formatExpires();
        }
        
        return $result;
    }
    
    /**
     * expires 属性を書式化します.
     * 
     * @return string "expires=Wdy, DD-Mon-YY HH:MM:SS GMT" 形式の文字列
     */
    private function formatExpires()
    {
        $format = CookieExpiresFormat::getInstance();
        $offset = Util::cleanTimeZoneOffset($this->timeZoneOffset);
        $date   = $format->format($this->expires, $offset);
        return "expires={$date}";
    }
}
