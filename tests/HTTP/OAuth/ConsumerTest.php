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
require_once 'HTTP/OAuth/Consumer.php';

class HTTP_OAuth_ConsumerTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $instance = new HTTP_OAuth_Consumer('key', 'secret');
        $this->assertType('HTTP_OAuth_Consumer', $instance);
        $this->assertEquals('key', $instance->getKey());
        $this->assertEquals('secret', $instance->getSecret());

        $instance = new HTTP_OAuth_Consumer('key', 'secret', 'token', 'tokenSecret');
        $this->assertType('HTTP_OAuth_Consumer', $instance);
        $this->assertEquals('key', $instance->getKey());
        $this->assertEquals('secret', $instance->getSecret());
        $this->assertEquals('token', $instance->getToken());
        $this->assertEquals('tokenSecret', $instance->getTokenSecret());
    }

    public function testGetRequestToken()
    {
        $res = $this->mockedResponse(array('oauth_token' => 'token',
            'oauth_token_secret' => 'token_secret'));
        $req = $this->mockedRequest($res);
        $con = $this->mockedConsumer($req);
        $con->getRequestToken('http://example.com/request_token');
        $this->assertEquals('token', $con->getToken());
        $this->assertEquals('token_secret', $con->getTokenSecret());
    }

    /**
     * @expectedException HTTP_OAuth_Consumer_Exception_InvalidResponse
     */
    public function testGetRequestTokenWithMissingData()
    {
        $res = $this->mockedResponse(array());
        $req = $this->mockedRequest($res);
        $con = $this->mockedConsumer($req);
        $con->getRequestToken('http://example.com/request_token');
        $this->assertEquals('token', $con->getToken());
        $this->assertEquals('token_secret', $con->getTokenSecret());
    }

    public function testGetAccessToken()
    {
        $res = $this->mockedResponse(array('oauth_token' => 'token',
            'oauth_token_secret' => 'token_secret'));
        $req = $this->mockedRequest($res);
        $con = $this->mockedConsumer($req);
        $con->getAccessToken('http://example.com/request_token');
        $this->assertEquals('token', $con->getToken());
        $this->assertEquals('token_secret', $con->getTokenSecret());
    }

    /**
     * @expectedException HTTP_OAuth_Consumer_Exception_InvalidResponse
     */
    public function testGetAccessTokenWithMissingData()
    {
        $res = $this->mockedResponse(array());
        $req = $this->mockedRequest($res);
        $con = $this->mockedConsumer($req);
        $con->getAccessToken('http://example.com/request_token');
        $this->assertEquals('token', $con->getToken());
        $this->assertEquals('token_secret', $con->getTokenSecret());
    }

    public function testGetAuthorizeUrl()
    {
        $con = new HTTP_OAuth_Consumer('key', 'secret', 'token');
        $this->assertEquals('http://example.com/?oauth_token=token',
            $con->getAuthorizeUrl('http://example.com/'));
    }

    public function testSetSignatureMethod()
    {
        $con = new HTTP_OAuth_Consumer('key', 'secret', 'token');
        $con->setSignatureMethod('PLAINTEXT');
        $this->assertEquals('PLAINTEXT', $con->getSignatureMethod());
    }

    public function testGetOAuthConsumerRequest()
    {
        $con = new HTTP_OAuth_Consumer('key', 'secret', 'token');
        $this->assertType('HTTP_OAuth_Consumer_Request',
            $con->getOAuthConsumerRequest('http://foo.com/'));
    }

    private function mockedConsumer($req)
    {

        $instance = $this->getMock('HTTP_OAuth_Consumer',
            array('getOAuthConsumerRequest'), array('key', 'secret', 'token',
            'token_secret'));
        $instance->expects($this->any())->method('getOAuthConsumerRequest')
            ->will($this->returnValue($req));

        return $instance;
    }

    private function mockedRequest($res)
    {
        $req = $this->getMock('HTTP_OAuth_Consumer_Request', array('send'),
            array('http://foo.com'));
        $req->expects($this->any())->method('send')
            ->will($this->returnValue($res));

        return $req;
    }

    private function mockedResponse($bodyData)
    {
        $res = $this->getMock('HTTP_OAuth_Consumer_Response',
            array('getDataFromBody'), array(new HttpMessage('')));
        $res->expects($this->any())->method('getDataFromBody')
            ->will($this->returnValue($bodyData));

        return $res;
    }

}

?>
