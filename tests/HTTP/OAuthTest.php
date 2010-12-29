<?php
/**
 * HTTP_OAuth
 *
 * Implementation of the OAuth specification
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive  
 * a copy of the New BSD License and are unable to obtain it through the web, 
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth_Provider
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth_Provider
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth.php';

class HTTP_OAuthTest extends PHPUnit_Framework_TestCase
{

    public function testBuildHTTPQuery()
    {
        $array = array(
            'oauth_consumer_key'     => 'e1nTvIGVCPkbfqZdIE7OyA',
            'oauth_token'            => 'kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_signature'        => '6WvHOHROOBkKcP3YrpnEHNbn1y4=',
            'oauth_timestamp'        => '1245711961',
            'oauth_nonce'            => 'EF35F352-6FB0-4CFD-98E2-136BC6507434',
            'oauth_version'          => '1.0'
        );

        $result = HTTP_OAuth::buildHTTPQuery($array);
        $this->assertEquals($result, 'oauth_consumer_key=e1nTvIGVCPkbfqZdIE7OyA&oauth_nonce=EF35F352-6FB0-4CFD-98E2-136BC6507434&oauth_signature=6WvHOHROOBkKcP3YrpnEHNbn1y4%3D&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1245711961&oauth_token=kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM&oauth_version=1.0');
    }

    public function testURLEncode()
    {
        $raw = 'http://www.joestump.net/~foobar';
        $exp = 'http%3A%2F%2Fwww.joestump.net%2F~foobar';
        $bad = 'http%3A%2F%2Fwww.joestump.net%2F%7Efoobar';
        $res = HTTP_OAuth::urlencode($raw);
        
        $this->assertEquals($exp, $res);
        $this->assertNotEquals($bad, $res);
    }

    public function testURLDecode()
    {
        $raw = 'http://www.joestump.net/~foobar foo';
        $exp = 'http%3A%2F%2Fwww.joestump.net%2F~foobar%20foo';
        $res = HTTP_OAuth::urldecode($exp);
        $this->assertEquals($res, $raw);

        $res = HTTP_OAuth::urldecode(array($exp));
        $this->assertEquals($res, array($raw));
    }

    public function testScalarCheck()
    {
        $o = HTTP_OAuth::urlencode(new stdClass);
        $this->assertInstanceOf('stdClass', $o);
    }

    public function testEmptyArrayBuildQuery()
    {
        $s = HTTP_OAuth::buildHttpQuery(array());
        $this->assertEquals('', $s);
    }

    public function testAttachLog()
    {
        foreach (explode(':', get_include_path()) as $path) {
            if (file_exists($path . '/Log.php')) {
                include_once 'Log.php';
            }
        }

        if (!class_exists('Log')) {
            $this->markTestSkipped();
        }

        $log = Log::factory('null');
        HTTP_OAuth::attachLog($log);
        $oauth = $this->getMock('HTTP_OAuth', array('foo'));
        $oauth->debug('foo');
        $oauth->info('foo');
        $oauth->err('foo');
        HTTP_OAuth::detachLog($log);
    }

}

?>
