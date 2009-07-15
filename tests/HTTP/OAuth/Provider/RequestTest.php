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
     * Headers 
     * 
     * @var array $headers Headers for every example OAuth request
     */
    protected $headers = array(
        'Host'       => 'twitter.com',
        'Accept'     => '*/*',
        'User-Agent' =>  __CLASS__
    );

    /**
     * @dataProvider requestProvider
     */
    public function testArrayAccess(HttpMessage $message)
    {
        $request = new HTTP_OAuth_Provider_Request($message);

        foreach ($this->params as $key => $val) {

            $this->assertTrue(
                isset($request[$key]), 
                'Key ' . $key . ' not present in request'
            );

            $this->assertEquals($val, $request[$key]);
        }
    }

    /**
     * @dataProvider requestProvider
     */
    public function testIteratorAggregate(HttpMessage $message)
    {
        $request = new HTTP_OAuth_Provider_Request($message);
        foreach ($request as $key => $val) {
            $this->assertEquals($this->params[$key], $val);
        }
    }

    /**
     * @dataProvider requestProvider
     */
    public function testCountable(HttpMessage $message)
    {
        $request = new HTTP_OAuth_Provider_Request($message);
        $this->assertEquals(count($this->params), count($request));
    }

    public function testIsValidSignature()
    {
        $message = $this->getPostRequest();
        $request = new HTTP_OAuth_Provider_Request($message);
        $this->assertTrue(
            $request->isValidSignature($this->consumerSecret, $this->tokenSecret)
        );
    }

    public function requestProvider()
    {
        return array(
            array($this->getGetRequest()),
            array($this->getPostRequest()),
            array($this->getAuthorizationRequest())
        );
    }

    /**
     * Create parameters string 
     * 
     * @return string Parameter string
     */
    protected function createParamsString()
    {
        $sets = array();
        foreach ($this->params as $key => $val) {
            $sets[] = $key . '=' . HTTP_OAuth::urlencode($val);
        }

        return implode('&', $sets);
    }

    /**
     * Add headers 
     * 
     * @param mixed $message Current message
     * @param array $headers Headers to add
     *
     * @return string Header
     */
    protected function addHeaders($message, array $headers) 
    {
        $sets = array();
        foreach ($headers as $header => $val) {
            $sets[] = $header . ': ' . $val; 
        }

        $message .= "\r\n";
        $message .= implode("\r\n", $sets);
        $message .= "\r\n\r\n";

        return $message;
    }

    /**
     * Get GET Request 
     * 
     * @return object|HttpMessage Instance of a GET HttpMessage
     */
    protected function getGetRequest()
    {
        $headers = $this->headers;
        $headers['Content-Length'] = 0;

        $msg = 'GET /oauth?' . $this->createParamsString() . ' HTTP/1.1';
        $msg = $this->addHeaders($msg, $headers);

        return HttpMessage::factory($msg);
    }

    /**
     * Get POST Request 
     * 
     * @return object|HttpMessage Instance of a POST HttpMessage
     */
    protected function getPostRequest()
    {
        $body = $this->createParamsString();

        $headers = $this->headers;
        $headers['Content-Length'] = strlen($body);
        $headers['Content-Type']   = 'application/x-www-form-urlencoded';

        $msg = 'POST /oauth/access_token HTTP/1.1';
        $msg = $this->addHeaders($msg, $headers) . $body;

        return HttpMessage::factory($msg);
    }

    /**
     * Get Authorization Request 
     * 
     * @return object|HttpMessage Instance of a HttpMessage with Auth headers
     */
    protected function getAuthorizationRequest()
    {
        $sets   = array();
        $sets[] = 'realm="http://pear.php.net/package/HTTP_OAuth_Provider"';

        foreach ($this->params as $key => $val) {
            $sets[] = $key . '="' . HTTP_OAuth::urlencode($val) . '"';
        }
 
        $headers = $this->headers;
        $headers['Content-Length'] = 0;
        $headers['Authorization']  = 'OAuth ' . implode(",\n\t", $sets);

        $msg = 'PUT /oauth HTTP/1.1';
        $msg = $this->addHeaders($msg, $headers);

        return HttpMessage::factory($msg);
    }
}

?>
