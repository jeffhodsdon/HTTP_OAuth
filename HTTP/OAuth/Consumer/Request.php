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
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */

require_once 'Validate.php';
require_once 'HTTP/OAuth/Message.php';
require_once 'HTTP/OAuth/Consumer/Response.php';
require_once 'HTTP/OAuth/Signature.php';
require_once 'HTTP/OAuth/Exception.php';

/**
 * HTTP_OAuth_Consumer_Request
 *
 * Class to make OAuth requests to a provider.  Given a url, consumer secret,
 * token secret, and HTTP method make and sign a request to send.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */
class HTTP_OAuth_Consumer_Request extends HTTP_OAuth_Message
{

    /**
     *  Auth type constants
     */
    const AUTH_HEADER = 1;
    const AUTH_POST   = 2;
    const AUTH_GET    = 3;

    /**
     * Auth type
     *
     * @var int $authType Authorization type
     */
    protected $authType = self::AUTH_HEADER;

    /**
     * Url
     *
     * @var string $url Url to request
     */
    protected $url = null;

    /**
     * HTTP Method
     *
     * @var string $message HTTP method to use
     */
    protected $method = null;

    /**
     * Secrets
     *
     * Consumer and token secrets that will be used to sign
     * the request
     *
     * @var array $secrets Array of consumer and token secret
     */
    protected $secrets = array('', '');

    static protected $methodMap = array(
        'GET'    => HttpRequest::METH_GET,
        'POST'   => HttpRequest::METH_POST,
        'PUT'    => HttpRequest::METH_PUT,
        'DELETE' => HttpRequest::METH_DELETE
    );


    /**
     * Construct
     *
     * Sets url, secrets, and http method
     *
     * @param string $url     Url to be requested
     * @param array  $secrets Array of consumer and token secret
     * @param string $method  HTTP method
     *
     * @return void
     */
    public function __construct($url, array $secrets = array(), $method = 'POST')
    {
        $this->setUrl($url);
        $this->setMethod($method);
        if (count($secrets)) {
            $this->setSecrets($secrets);
        }
    }

    /**
     * Sets a url
     *
     * @param string $url Url to request
     *
     * @return void
     * @throws HTTP_OAuth_Exception on invalid url
     */
    public function setUrl($url)
    {
        if (!Validate::uri($url)) {
            throw new InvalidArgumentException("Invalid url: $url");
        }

        $this->url = $url;
    }

    /**
     * Sets consumer/token secrets array
     *
     * @param array $secrets Array of secrets to set
     *
     * @return void
     */
    public function setSecrets(array $secrets = array())
    {
        if (count($secrets) == 1) {
            $secrets[1] = '';
        }

        $this->secrets = $secrets;
    }

    /**
     * Gets secrets
     *
     * @return array Secrets array
     */
    public function getSecrets()
    {
        return $this->secrets;
    }

    /**
     * Sets authentication type
     *
     * Valid auth types are self::AUTH_HEADER, self::AUTH_POST,
     * and self::AUTH_GET
     *
     * @param int $type Auth type defined by this class constants
     *
     * @return void
     */
    public function setAuthType($type)
    {
        static $valid = array(self::AUTH_HEADER, self::AUTH_POST,
            self::AUTH_GET);
        if (!in_array($type, $valid)) {
            throw new InvalidArgumentException('Invalid Auth Type, see class ' .
                'constants');
        }

        $this->authType = $type;
    }

    /**
     * Gets authentication type
     *
     * @return int Set auth type
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * Sends request
     *
     * Builds and sends the request. This will sign the request with
     * the given secrets at self::$secrets.
     *
     * @return HTTP_OAuth_Consumer_Response Response instance
     * @throws HTTP_OAuth_Exception when request fails
     */
    public function send()
    {
        $request = $this->buildRequest();
        try {
            $response = $request->send();
        } catch (Exception $e) {
            throw new HTTP_OAuth_Exception($request->getResponseInfo('error'));
        }

        return new HTTP_OAuth_Consumer_Response($response);
    }

    /**
     * Builds request for sending
     *
     * Adds timestamp, nonce, signs, and creates the HttpRequest object.
     *
     * @return HttpRequest Instance of the request object ready to send()
     */
    protected function buildRequest()
    {
        $sig = HTTP_OAuth_Signature::factory($this->getSignatureMethod());

        $this->oauth_timestamp = time();
        $this->oauth_nonce     = md5(microtime(true) . rand(1, 999));
        $this->oauth_version   = '1.0';
        $this->oauth_signature = $sig->build(
            $this->getMethod(true), $this->getUrl(), $this->getParameters(),
            $this->secrets[0], $this->secrets[1]
        );

        $request = $this->getHttpRequest($this->url);
        $request->setMethod($this->getMethod());
        $request->addHeaders(array('Expect' => ''));
        $params = $this->getOAuthParameters();
        switch ($this->getAuthType()) {
        case self::AUTH_HEADER:
            $auth = $this->getAuthForHeader($params);
            $request->addHeaders(array('Authorization' => $auth));
            break;
        case self::AUTH_POST:
            $request->addPostFields(HTTP_OAuth::urlencode($params));
            break;
        case self::AUTH_GET:
            $request->addQueryData(HTTP_OAuth::urlencode($params));
            break;
        }

        return $request;
    }

    /**
     * Gets a HttpRequest instance
     *
     * @param string $url Url to be requested
     *
     * @return HttpRequest Instance of the request object
     */
    public function getHttpRequest($url)
    {
        return new HttpRequest($url);
    }

    /**
     * Creates OAuth header
     *
     * Given the passed in OAuth parameters, put them together
     * in a formated string for a Authorization header.
     *
     * @param array $params OAuth parameters
     *
     * @return void
     */
    protected function getAuthForHeader(array $params)
    {
        $url    = parse_url($this->url);
        $realm  = $url['scheme'] . '://' . $url['host'] . '/';
        $header = 'OAuth realm="' . $realm . '"';
        foreach ($params as $name => $value) {
            $header .= ", " . HTTP_OAuth::urlencode($name) . '="' .
                HTTP_OAuth::urlencode($value) . '"';
        }

        return $header;
    }

    /**
     * Sets request method
     *
     * @param string $method HTTP Request method to use
     *
     * @return void
     * @throws InvalidArgumentException on unsupported HTTP request method
     */
    public function setMethod($method)
    {
        if (!array_key_exists($method, self::$methodMap)) {
            throw new InvalidArgumentException('Unsupported HTTP method');
        }

        $this->method = self::$methodMap[$method];
    }

    /**
     * Gets request method
     *
     * @return string HTTP request method
     */
    public function getMethod($string = false)
    {
        if ($string) {
            $map = array_flip(self::$methodMap);
            return $map[$this->method];
        }

        return $this->method;
    }

    /**
     * Gets url
     *
     * @return string Url to request
     */
    public function getUrl()
    {
        return $this->url;
    }

}

?>
