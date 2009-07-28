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
     * Message
     *
     * @var HttpMessage $message Instance of HttpMessage
     */
    protected $message = null;

    /**
     * Construct
     *
     * @param HttpMessage $message Optional current HttpMessage
     *
     * @return void
     */
    public function __construct(HttpMessage $message = null)
    {
        $this->message = $message;
        if ($message === null) {
            $this->message = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);
        }

        $this->setParametersFromRequest();
    }

    /**
     * Set parameters from request
     *
     * @return void
     */
    protected function setParametersFromRequest()
    {
        $auth   = $this->message->getHeader('Authorization');
        $params = array();
        if ($auth !== null) {
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

                $params[$key] = HTTP_OAuth::urldecode($value);
            }
        } else if ($this->message->getRequestMethod() == 'POST') {
            $contentType = $this->message->getHeader('Content-Type');
            if ($contentType !== 'application/x-www-form-urlencoded') {
                throw new HTTP_OAuth_Provider_Exception_InvalidRequest('Invalid ' .
                    'content type for POST request');
            }

            parse_str($this->message->getBody(), $params);
        } else {
            parse_str(
                parse_url($this->message->getRequestUrl(), PHP_URL_QUERY), $params
            );
        }

        if (empty($params)) {
            throw new HTTP_OAuth_Provider_Exception_InvalidRequest('No oauth data ' .
                'found from request');
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

        return ($this->oauth_signature === $check);
    }

    /**
     * Get signature base string
     *
     * Useful for debugging invalid signatures
     *
     * @return string Signature base string
     */
    public function getSignatureBaseString()
    {
        $sign = HTTP_OAuth_Signature::factory($this->oauth_signature_method);
        return $sign->getBase(
            $this->getRequestMethod(), $this->getUrl(), $this->getParameters()
        );
    }

    /**
     * Get request method
     *
     * @return string Request method
     */
    public function getRequestMethod()
    {
        return $this->message->getRequestMethod();
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

        return $schema . '://' . $this->message->getHeader('Host')
            . $this->message->getRequestUrl();
    }

}

?>
