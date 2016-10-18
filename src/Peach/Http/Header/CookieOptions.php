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
     * path 属性をあらわす文字列です.
     * 
     * @var string
     */
    private $path;
    
    /**
     * secure 属性をあらわす論理値です.
     * true の場合のみ secure 属性が付与されます.
     * 
     * @var bool
     */
    private $secure;
    
    /**
     * 属性を何も持たない, 新しい CookieOptions オブジェクトを構築します.
     */
    public function __construct()
    {
        $this->secure = false;
        $this->httpOnly = false;
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
     * path 属性の値をセットします.
     * 引数に null をセットした場合は path 属性を削除します.
     * 
     * @param string $path
     */
    public function setPath($path)
    {
        if (!$this->validatePath($path)) {
            throw new InvalidArgumentException("Invalid path: '{$path}'");
        }
        $this->path = $path;
    }
    
    /**
     * 指定された文字列が RFC 3986 にて定義される URI のパス文字列として妥当かどうかを検証します.
     * フォーマットは以下の BNF 記法に基づきます.
     * 
     * <pre>
     * path-absolute = "/" [ segment-nz *( "/" segment ) ]
     * segment       = *pchar
     * segment-nz    = 1*pchar
     * pchar         = unreserved / pct-encoded / sub-delims / ":" / "@"
     * unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
     * pct-encoded   = "%" HEXDIG HEXDIG
     * sub-delims    = "!" / "$" / "&" / "'" / "(" / ")"
     * </pre>
     * 
     * @param  string $path 検査対象のパス
     * @return bool         引数がパスとして妥当な場合のみ true
     */
    private function validatePath($path)
    {
        if ($path === null) {
            return true;
        }
        
        $classUnreserved = "a-zA-Z0-9\\-\\._~";
        $classSubDelims  = "!\$&'\\(\\)";
        $classOthers     = ":@";
        $validChars      = "[{$classUnreserved}{$classSubDelims}{$classOthers}]";
        $pctEncoded      = "%[0-9a-fA-F]{2}";
        $pchar           = "{$validChars}|{$pctEncoded}";
        $segment         = "({$pchar})*";
        $segmentNz       = "({$pchar})+";
        $pathAbsolute    = "\\/({$segmentNz}(\\/{$segment})*)?";
        return preg_match("/\\A{$pathAbsolute}\\z/", $path);
    }
    
    /**
     * path 属性の値を返します.
     * もしも path 属性がセットされていない場合は null を返します.
     * 
     * @return string path 属性の値. セットされていない場合は null
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * secure 属性をセットします.
     * もしも引数が true の場合は secure 属性を有効化, false の場合は無効化します.
     * 
     * @param bool $secure secure 属性を有効化する場合は true, 無効化する場合は false
     */
    public function setSecure($secure)
    {
        $this->secure = (bool) $secure;
    }
    
    /**
     * secure 属性が有効かどうかを判定します.
     * secure 属性が有効な場合は true, そうでない場合は false を返します.
     * もしもこのオブジェクトの setSecure() を一度も実行していない場合,
     * secure 属性は無効となるため false を返します.
     * 
     * @return bool secure 属性が有効な場合は true, そうでない場合は false
     */
    public function hasSecure()
    {
        return $this->secure;
    }
    
    /**
     * httponly 属性をセットします.
     * もしも引数が true の場合は httponly 属性を有効化, false の場合は無効化します.
     * 
     * @param bool $httpOnly httponly 属性を有効化する場合は true, 無効化する場合は false
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = (bool) $httpOnly;
    }
    
    /**
     * httponly 属性が有効かどうかを判定します.
     * httponly 属性が有効な場合は true, そうでない場合は false を返します.
     * もしもこのオブジェクトの setHttpOnly() を一度も実行していない場合,
     * httponly 属性は無効となるため false を返します.
     * 
     * @return bool httponly 属性が有効な場合は true, そうでない場合は false
     */
    public function hasHttpOnly()
    {
        return $this->httpOnly;
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
        if ($this->maxAge !== null) {
            $result[] = "max-age={$this->maxAge}";
        }
        if ($this->domain !== null) {
            $result[] = "domain={$this->domain}";
        }
        if ($this->path !== null) {
            $result[] = "path={$this->path}";
        }
        if ($this->secure) {
            $result[] = "secure";
        }
        if ($this->httpOnly) {
            $result[] = "httponly";
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
