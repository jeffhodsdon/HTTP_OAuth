<?php
/**
 * HTTP_OAuth
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

require_once 'HTTP/OAuth/Message.php';
require_once 'HTTP/OAuth/Signature.php';
require_once 'HTTP/OAuth/Provider/Exception/InvalidRequest.php';

/**
 * HTTP_OAuth_Provider_Request
 *
 * NOTE:
 * Currently, it's up to the developer to implement the provider side of
 * timestamp and nonce checking.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */
class HTTP_OAuth_Provider_Request extends HTTP_OAuth_Message
{

    /**
     * Headers from the incoming request
     *
     * @var array $headers Headers from the incoming request
     */
    protected $headers = array();

    /**
     * Method used in the incoming request
     * 
     * @var string Method used in the incoming request
     */
    protected $method = '';

    /**
     * Body data from the incoming request
     *
     * @var string raw body data from the incoming request
     */
    protected $body = '';

    /**
     * Construct
     *
     * @param string $body optional. The HTTP request body. Use this if your
     *                     framework automatically reads the php://input
     *                     stream.
     *
     * @return void
     */
    public function __construct($body = '')
    {
        $this->setHeaders();
        $this->setBody($body);
        $this->setParametersFromRequest();
    }

    /**
     * Sets the body data for this request
     *
     * This is useful if your framework automatically reads the php://input
     * stream and your API puts parameters in the request body.
     *
     * @param string $body the HTTP request body.
     *
     * @return HTTP_OAuth_Provider_Request the current object, for fluent
     *                                     interface.
     */
    public function setBody($body = '')
    {
        if (empty($body)) {
            // @codeCoverageIgnoreStart
            $this->body = file_get_contents('php://input');
            // @codeCoverageIgnoreEnd
        } else {
            $this->body = (string)$body;
        }

        return $this;
    }

    /**
     * Set incoming request headers
     *
     * @param array $headers Optional headers to set
     *
     * @return void
     */
    public function setHeaders(array $headers = array())
    {
        if (count($headers)) {
            $this->headers = $headers;
        } else if (is_array($this->apacheRequestHeaders())) {
            $this->debug('Using apache_request_headers() to get request headers');
            $this->headers = $this->apacheRequestHeaders();
        } else if (is_array($this->peclHttpHeaders())) {
            $this->debug('Using pecl_http to get request headers');
            $this->headers = $this->peclHttpHeaders();
        } else {
            $this->debug('Using $_SERVER to get request headers');
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $name = str_replace(
                        ' ', '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                    );
                    $this->headers[$name] = $value;
                }
            }
        }
    }

    /**
     * Apache request headers
     *
     * If the function exists to get the request headers from apache
     * use it to get them, otherwise return null. Abstracted for
     * testing purposes.
     *
     * @return array|null Headers or null if no function
     */
    protected function apacheRequestHeaders()
    {
        if (function_exists('apache_request_headers')) {
            // @codeCoverageIgnoreStart
            return apache_request_headers();
            // @codeCoverageIgnoreEnd
        }

        return null;
    }

    // @codeCoverageIgnoreStart
    /**
     * Pecl HTTP request headers
     *
     * If the pecl_http extension is loaded use it to get the incoming
     * request headers, otherwise return null. Abstracted for testing
     * purposes.
     *
     * @return array|null Headers or null if no extension
     */
    protected function peclHttpHeaders()
    {
        if (extension_loaded('http') && class_exists('HttpMessage')) {
            $message = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);
            return $message->getHeaders();
        }

        return null;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Set parameters from the incoming request 
     * 
     * @return void
     */
    public function setParametersFromRequest()
    {
        $params = array();
        $auth   = $this->getHeader('Authorization');
        if ($auth !== null) {
            $this->debug('Using OAuth data from header');

            // strip leading OAuth authentication scheme
            $auth = preg_replace('/^oauth /i', '', $auth);

            // split auth parameters
            $parts = explode(',', $auth);
            foreach ($parts as $part) {
                list($key, $value) = explode('=', $part, 2);

                // strip spaces from around comma and equals delimiters
                $key = trim($key);
                $value = trim($value);

                // ignore auth parameters that are not prefixed with oauth_
                if (substr(strtolower($key), 0, 6) != 'oauth_') {
                    continue;
                }

                $value = str_replace('"', '', $value);

                $params[HTTP_OAuth::urldecode($key)] = HTTP_OAuth::urldecode($value);
            }
        }

        // RFC 5849 3.4.1.3.1 allows any HTTP method with the correct
        // content-type and body content. Don't check method type here.
        // See PEAR Bug 17806.
        $contentType = $this->getHeader('Content-Type');
        if (strncmp($contentType, 'application/x-www-form-urlencoded', 33) === 0) {
            $this->debug('getting application/x-www-form-urlencoded data from request body');
            $params = array_merge(
                $params,
                $this->parseQueryString($this->getBody())
            );
        }

        $params = array_merge(
            $params,
            $this->parseQueryString($this->getQueryString())
        );

        if (empty($params)) {
            throw new HTTP_OAuth_Provider_Exception_InvalidRequest('No oauth ' .
                'data found from request');
        }

        $this->setParameters($params);
    }

    /**
     * Is valid signature
     *
     * @param string $consumerSecret Consumer secret value
     * @param string $tokenSecret    Token secret value (if exists)
     *
     * @return bool Valid or not
     */
    public function isValidSignature($consumerSecret, $tokenSecret = '')
    {
        if (!$this->oauth_signature_method) {
            throw new HTTP_OAuth_Provider_Exception_InvalidRequest(
                'Missing oauth_signature_method in request'
            );
        }

        $sign  = HTTP_OAuth_Signature::factory($this->oauth_signature_method);
        $check = $sign->build(
            $this->getRequestMethod(), $this->getUrl(),
            $this->getParameters(), $consumerSecret, $tokenSecret
        );

        if ($this->oauth_signature === $check) {
            $this->info('Valid signature');
            return true;
        }

        $this->err('Invalid signature');
        return false;

    }

    /**
     * Gets incoming request query string
     *
     * @return string|null Query string
     */
    public function getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['QUERY_STRING'];
        }

        return null;
    }

    /**
     * Get request method
     *
     * @return string Request method
     */
    public function getRequestMethod()
    {
        if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
            return 'HEAD';
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get url
     *
     * @return string URL of the request
     */
    public function getUrl()
    {
        $schema = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $schema .= 's';
        }

        return $schema . '://' . $this->getHeader('Host')
            . $this->getRequestUri();
    }

    /**
     * Gets incoming request URI
     *
     * Checks if the schema/host is included and strips it.
     * Thanks Naosumi! Bug #16800
     *
     * @return string|null Request URI, null if doesn't exist
     */
    public function getRequestUri()
    {
        if (!array_key_exists('REQUEST_URI', $_SERVER)) {
            return null;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $pos = stripos($uri, '://');
        if (!$pos) {
            return $uri;
        }

        return substr($uri, strpos($uri, '/', $pos + 3));
    }

    /**
     * Gets a header
     *
     * @param string $header Which header to fetch
     *
     * @return string|null Header if exists, null if not
     */
    public function getHeader($header)
    {
        foreach ($this->headers as $name => $value) {
            if (strtolower($header) == strtolower($name)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * getHeaders 
     * 
     * @access public
     * @return void
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Gets request body
     *
     * @return string request data
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Parses a query string
     *
     * Does not use built-in urldecoding of name or values like $_GET and
     * $_POST. Instead, names and values are decoded using RFC 3986 as required
     * by OAuth.
     *
     * @param string $string Query string
     *
     * @return array Data from the query string
     */
    protected function parseQueryString($string)
    {
        $data = array();
        if (empty($string)) {
            return $data;
        }

        foreach (explode('&', $string) as $part) {
            if (!strstr($part, '=')) {
                continue;
            }

            list($key, $value) = explode('=', $part);

            $key   = HTTP_OAuth::urldecode($key);
            $value = HTTP_OAuth::urldecode($value);

            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    $data[$key][] = $value;
                } else {
                    $data[$key] = array(
                        $data[$key],
                        $value
                    );
                }
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

}

?>
