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
require_once 'tests/HTTP/OAuth/MessageMock.php';

class HTTP_OAuth_MessageTest extends PHPUnit_Framework_TestCase
{

    public function testGetOAuthParameters()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['token']        = 'foo';
        $m['consumer_key'] = 'key';
        $m['w000t']        = 'w000t';

        $p = $m->getOAuthParameters();
        $this->assertEquals(
            $p, array('oauth_consumer_key' => 'key', 'oauth_token' => 'foo')
        );
    }

    public function testSetParameters()
    {
        $p = array(
            'oauth_token'        => 'foo',
            'oauth_consumer_key' => 'key',
            'bar'          => 'w00t'
        );

        $m = new HTTP_OAuth_MessageMock;
        $m->setParameters($p);
        $this->assertEquals($m->getParameters(), $p);
    }

    public function testGetSignatureMethod()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m->signature_method = 'HMAC-SHA1';
        $this->assertEquals($m->getSignatureMethod(), 'HMAC-SHA1');
    }

    public function testGetParametersIsSorted()
    {
        $params = array('z' => 'foo', 'a' => 'bar');
        $m = new HTTP_OAuth_MessageMock;
        $m->setParameters($params);
        $this->assertEquals('bar', reset($m->getParameters()));
    }

    public function testMagicGetter()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m->signature_method = 'HMAC-SHA1';
        $this->assertEquals($m->signatureMethod, 'HMAC-SHA1');
 
    }

    public function testOffestExists()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['foo'] = 'www';

        $this->assertInstanceOf('ArrayAccess', $m);
        $this->assertTrue(isset($m['foo']));
        $this->assertFalse(isset($m['bar']));
    }

    public function testOffsetGet()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['foo'] = 'www';

        $this->assertInstanceOf('ArrayAccess', $m);
        $this->assertEquals($m['foo'], 'www');
    }

    public function testOffsetUnset()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['foo'] = 'www';

        $this->assertInstanceOf('ArrayAccess', $m);
        $this->assertEquals($m['foo'], 'www');

        unset($m['foo']);

        $this->assertFalse(isset($m['foo']));
    }

    public function testGetIterator()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['foo'] = 'www';
        $m['bar'] = 'http';

        $this->assertInstanceOf('IteratorAggregate', $m);

        $i = $m->getIterator();
        $this->assertInstanceOf('ArrayIterator', $i);

        $i = 0;
        foreach ($m as $key => $value) {
            $i++;
        }

        $this->assertEquals(2, $i);
    }

    public function testCount()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['foo'] = 'www';
        $m['bar'] = 'http';

        $this->assertInstanceOf('Countable', $m);
        $this->assertEquals(count($m), 2);
    }

}

?>
