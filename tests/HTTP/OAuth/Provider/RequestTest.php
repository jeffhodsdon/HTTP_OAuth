<?php
/**
 * HTTP_OAuth_Provider_RequestTest
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
 * @package   HTTP_OAuth_Provider
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth_Provider
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth_Provider
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Provider/Request.php';

/**
 * HTTP_OAuth_Provider_RequestTest
 * 
 * @category  HTTP
 * @package   HTTP_OAuth_Provider
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class HTTP_OAuth_Provider_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Consumer secret 
     * 
     * @var string $consumerSecret Consumer secret
     */
    protected $consumerSecret = 's85GLpyelma8rvNCgOjxi3lBXoedqsoDas6OYIQCeI';

    /**
     * Token secret 
     * 
     * @var string $tokenSecret Token secret
     */
    protected $tokenSecret = 'fluoBMLdReBOPsmjBfsVP3lslUAO9tVrLsIxQsTyc';

    /**
     * Parameters 
     * 
     * @var array $params Parameters for an example OAuth request
     */
    protected $params = array(
        'oauth_consumer_key'     => 'e1nTvIGVCPkbfqZdIE7OyA',
        'oauth_token'            => 'kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM',
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_signature'        => '6WvHOHROOBkKcP3YrpnEHNbn1y4=',
        'oauth_timestamp'        => '1245711961',
        'oauth_nonce'            => 'EF35F352-6FB0-4CFD-98E2-136BC6507434',
        'oauth_version'          => '1.0'
    );

    /**
     * @expectedException HTTP_OAuth_Provider_Exception_InvalidRequest
     */
    public function testConstruct()
    {
        $req = new HTTP_OAuth_Provider_Request;
    }

    public function testArrayAccess()
    {
        $request = $this->mockedRequest();
        foreach ($this->params as $key => $val) {

            $this->assertTrue(
                isset($request[$key]), 
                'Key ' . $key . ' not present in request'
            );

            $this->assertEquals($val, $request[$key]);
        }
    }

    public function testCountable()
    {
        $request = $this->mockedRequest();
        $this->assertEquals(count($this->params), count($request));
    }

    public function testSetHeaders()
    {
        $request = $this->mockedRequest();
        $request->setHeaders();
    }

    public function testSetParametersFromRequest()
    {
        $header = 'Authorization: OAuth realm="", oauth_consumer_key="key", oauth_signature_method="HMAC-SHA1", oauth_signature="ZUgC96UBRxYOl1Pml32hNDsNNUc%3D", oauth_timestamp="1251304744", oauth_nonce="18B2129F-4A4E-4502-8EB5-801DE2BB0247", oauth_version="1.0"';
        $queryString = 'oauth_signature_method=HMAC-SHA1&oauth_consumer_key=key&oauth_token=kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM&oauth_signature=ZUgC96UBRxYOl1Pml32hNDsNNUc%3D&oauth_timestamp=1251304744&oauth_nonce=18B2129F-4A4E-4502-8EB5-801DE2BB0247&oauth_version=1.0';
        $expected = array(
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_consumer_key'     => 'key',
            'oauth_token'            => 'kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM',
            'oauth_signature'        => 'ZUgC96UBRxYOl1Pml32hNDsNNUc=',
            'oauth_timestamp'        => '1251304744',
            'oauth_nonce'            => '18B2129F-4A4E-4502-8EB5-801DE2BB0247',
            'oauth_version'          => '1.0'
        );

        $request = $this->mockedRequest();
        $request->setHeaders(array('Authorization' => $header));
        $request->setParametersFromRequest();
        $this->assertEquals($expected, $request->getParameters());
        $this->assertEquals(array('Authorization' => $header), $request->getHeaders());

        $_POST = $expected;
        $request = $this->mockedRequest(array('getRequestMethod'));
        $request->expects($this->any())->method('getRequestMethod')
            ->will($this->returnValue('POST'));
        $request->setHeaders(
            array('Content-Type' => 'application/x-www-form-urlencoded'));
        $request->setParametersFromRequest();
        $this->assertEquals($expected, $request->getParameters());

        $request = $this->mockedRequest(array('getRequestMethod', 'getQueryString'));
        $request->expects($this->any())->method('getRequestMethod')
            ->will($this->returnValue('GET'));
        $request->expects($this->any())->method('getQueryString')
            ->will($this->returnValue($queryString));
        $request->setParametersFromRequest();
        $this->assertEquals($expected, $request->getParameters());
    }

    /**
     * @expectedException HTTP_OAuth_Provider_Exception_InvalidRequest
     */
    public function testInvalidPOSTContentType()
    {
        $request = $this->mockedRequest(array('getRequestMethod'));
        $request->expects($this->any())->method('getRequestMethod')
            ->will($this->returnValue('POST'));
        $request->setHeaders(array('Content-Type' => 'foo'));
        $request->setParametersFromRequest();
    }

    public function testIsValidSignature()
    {
        $request = $this->mockedRequest();
        $result  = $request->isValidSignature($this->consumerSecret,
            $this->tokenSecret);
        $this->assertFalse($result);
    }

    public function testGetQueryString()
    {
        unset($_SERVER['QUERY_STRING']);
        $request = $this->mockedRequest();
        $this->assertNull($request->getQueryString());
        $_SERVER['QUERY_STRING'] = 'foo';
        $this->assertEquals('foo', $request->getQueryString());
    }

    public function testGetRequestMethod()
    {
        unset($_SERVER['REQUEST_METHOD']);
        $request = $this->mockedRequest();
        $this->assertEquals('HEAD', $request->getRequestMethod());
        $_SERVER['REQUEST_METHOD'] = 'foo';
        $this->assertEquals('foo', $request->getRequestMethod());
    }

    public function testGetRequestUri()
    {
        unset($_SERVER['REQUEST_URI']);
        $request = $this->mockedRequest();
        $this->assertNull($request->getRequestUri());
        $_SERVER['REQUEST_URI'] = 'foo';
        $this->assertEquals('foo', $request->getRequestUri());
    }

    public function testGetUrl()
    {
        unset($_SERVER['REQUEST_URI']);
        $request = $this->mockedRequest();
        $request->setHeaders(array('foo' => 'foo'));
        $_SERVER['HTTPS'] = 'off';
        $this->assertEquals('http://', $request->getUrl());
        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals('https://', $request->getUrl());

    }

    protected function mockedRequest(array $methods = array('foo'))
    {
        $request = $this->getMock('HTTP_OAuth_Provider_Request', $methods,
            array(), 'HTTP_OAuth_Provider_RequestMock' . rand(1, 99999), false);
        $request->setParameters($this->params);

        return $request;
    }

}

?>
