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
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->setHeaders();
        $this->setParametersFromRequest();
    }

    /**
     * Set incoming request headers
     *
     * @return void
     */
    public function setHeaders()
    {
        if (function_exists('apache_request_headers')) {
            $this->debug('Using apache_request_headers() to get request headers');
            $this->headers = apache_request_headers();
        } else if (extension_loaded('http') && class_exists('HttpMessage')) {
            $this->debug('Using pecl_http to get request headers');
            $message = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);
            $this->headers = $message->getHeaders();
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
            $parts = explode(',', $auth);
            foreach ($parts as $part) {
                list($key, $value) = explode('=', trim($part));
                if (strstr(strtolower($key), 'oauth ')
                    || strstr(strtolower($key), 'uth re')
                ) {
                    continue;
                }

                $value = trim($value);
                $value = str_replace('"', '', $value);

                $params[$key] = $value;
            }
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->debug('Using OAuth data from POST');
            $contentType = $this->getHeader('Content-Type');
            if ($contentType !== 'application/x-www-form-urlencoded') {
                throw new HTTP_OAuth_Provider_Exception_InvalidRequest('Invalid ' .
                    'content type for POST request');
            }

            if (!empty($HTTP_RAW_POST_DATA)) {
                parse_str($HTTP_RAW_POST_DATA, $params);
            } else {
                $params = $_POST;
            }
        } else {
            $this->debug('Using OAuth data from GET');
            if (!empty($_SERVER['QUERY_STRING'])) {
                parse_str($_SERVER['QUERY_STRING'], $params);
            } else {
                $params = $_GET;
            }
        }

        if (empty($params)) {
            throw new HTTP_OAuth_Provider_Exception_InvalidRequest('No oauth ' .
                'data found from request');
        }

        $this->setParameters(HTTP_OAuth::urldecode($params));
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
     * Gets the incoming request url
     *
     * @return string Requested url
     */
    public function getRequestUrl()
    {
        return $_SERVER['REQUEST_URI'];
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
            . $this->getRequestUrl();
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
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
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

}

?>
