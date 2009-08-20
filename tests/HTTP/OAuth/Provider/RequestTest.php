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

    protected function mockedRequest()
    {
        $request = $this->getMock('HTTP_OAuth_Provider_Request', array('foo'),
            array(), 'HTTP_OAuth_Provider_RequestMock' . rand(1, 99999), false);
        $request->setParameters($this->params);

        return $request;
    }

}

?>
