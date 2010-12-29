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
require_once 'HTTP/OAuth/Consumer/Request.php';
require_once 'HTTP/Request2.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

class HTTP_OAuth_Consumer_RequestTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/',
            array('consumer', 'token'));
        $this->assertInstanceOf('HTTP_OAuth_Consumer_Request', $req);
        $this->assertEquals(array('consumer', 'token'), $req->getSecrets());
    }

    public function testSetUrl()
    {
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/');
        $this->assertEquals('http://example.com/', $req->getUrl()->getURL());
    }

    public function testSetSecrets()
    {
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/');
        $req->setSecrets(array('consumer'));
        $this->assertEquals(array('consumer', ''), $req->getSecrets());
    }

    public function testSetAuthType()
    {
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/');
        $req->setAuthType(HTTP_OAuth_Consumer_Request::AUTH_POST);
        $this->assertEquals(HTTP_OAuth_Consumer_Request::AUTH_POST,
            $req->getAuthType());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidAuthType()
    {
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/');
        $req->setAuthType('POST');
    }

    public function testSend()
    {
        $mockAdapter = new HTTP_Request2_Adapter_Mock;
        $mockAdapter->addResponse("HTTP/1.1 200 OK\n\nfoo");
        $mockReq = new HTTP_Request2('http://example.com');
        $mockReq->setAdapter($mockAdapter);
        $req = new HTTP_OAuth_Consumer_Request;
        $req->accept($mockReq);
        $res = $req->send();
        $this->assertInstanceOf('HTTP_OAuth_Consumer_Request', $req);
        $this->assertInstanceOf('HTTP_OAuth_Consumer_Response', $res);
        $this->assertEquals('foo', $res->getBody());
    }

    /**
     * testAccept 
     * 
     * @return void
     */
    public function testAccept()
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
        $req = new HTTP_OAuth_Consumer_Request('http://example.com/');
        $req->accept($log);
    }

    public function testNoOAuthParametersInGET()
    {
        $mockReq = new HTTP_Request2('http://example.com');
        $mockReq->setAdapter(new HTTP_Request2_Adapter_Mock);
        $req = new HTTP_OAuth_Consumer_Request;
        $req->accept($mockReq);
        $req->foo = 'bar';
        $req->oauth_consumer_key = 'key';
        $req->send();
        $this->assertEquals('http://example.com/?foo=bar',
            $req->getUrl()->getUrl());
    }

}

?>
