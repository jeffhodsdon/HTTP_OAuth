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

/**
 * HTTP_OAuth_Provider_Response
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */
class HTTP_OAuth_Provider_Response extends HTTP_OAuth_Message
{

    const STATUS_UNSUPPORTED_PARAMETER        = 0;
    const STATUS_UNSUPPORTED_SIGNATURE_METHOD = 1;
    const STATUS_MISSING_REQUIRED_PARAMETER   = 2;
    const STATUS_DUPLICATED_OAUTH_PARAMETER   = 3;

    const STATUS_INVALID_CONSUMER_KEY = 4;
    const STATUS_INVALID_TOKEN        = 5;
    const STATUS_INVALID_SIGNATURE    = 6;
    const STATUS_INVALID_NONCE        = 7;

    /**
     * Status map
     *
     * Map of what statuses have codes and body text
     *
     * @var array $statusMap Map of status to code and text
     */
    static protected $statusMap = array(
        self::STATUS_UNSUPPORTED_PARAMETER => array(
            400, 'Unsupported parameter'
        ),
        self::STATUS_UNSUPPORTED_SIGNATURE_METHOD => array(
            400, 'Unsupported signature method'
        ),
        self::STATUS_MISSING_REQUIRED_PARAMETER => array(
            400, 'Missing required parameter'
        ),
        self::STATUS_DUPLICATED_OAUTH_PARAMETER => array(
            400, 'Duplicated OAuth Protocol Parameter'
        ),
        self::STATUS_INVALID_CONSUMER_KEY => array(
            401, 'Invalid Consumer Key'
        ),
        self::STATUS_INVALID_TOKEN => array(
            401, 'Invalid / expired Token'
        ),
        self::STATUS_INVALID_SIGNATURE => array(
            401, 'Invalid signature'
        ),
        self::STATUS_INVALID_NONCE => array(
            401, 'Invalid / used nonce'
        )
    );

    /**
     * Message
     *
     * @var HttpMessage $message HTTP message instance
     */
    protected $message = null;

    /**
     * Construct
     *
     * @param HttpMessage $message Optional existing HTTP message instance
     *
     * @return void
     */
    public function __construct(HttpMessage $message = null)
    {
        if ($message === null) {
            $message = HttpMessage::fromEnv(HttpMessage::TYPE_RESPONSE);
        }

        $this->setMessage($message);
        $this->setHeader('WWW-Authenticate', 'OAuth');
    }

    /**
     * Set realm
     *
     * @param string $realm Realm for the WWW-Authenticate header
     *
     * @return void
     */
    public function setRealm($realm)
    {
        $header = 'OAuth realm="' . $realm . '"';
        $this->setHeader('WWW-Authenticate', $header);
    }

    /**
     * Set header
     *
     * @param string $name  Name of the header
     * @param string $value Value of the header
     *
     * @return void
     */
    public function setHeader($name, $value)
    {
        $headers        = $this->getHeaders();
        $headers[$name] = $value;
        $this->setHeaders($headers);
    }

    /**
     * Set status
     *
     * @param int $status Status constant
     *
     * @return void
     */
    public function setStatus($status)
    {
        if (!array_key_exists($status, self::$statusMap)) {
            throw new HTTP_OAuth_Exception('Invalid status');
        }

        list($code, $text) = self::$statusMap[$status];
        $this->setResponseCode($code);
        $this->setBody($text);
    }

    /**
     * Get message
     *
     * @return HttpMessage Instance of HttpMessage
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param HttpMessage $message Message to set
     *
     * @return void
     */
    public function setMessage(HttpMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Prepare body
     *
     * Sets the body if nesscary
     *
     * @return void
     */
    protected function prepareBody()
    {
        if ($this->getBody() === '') {
            $this->setBody(HTTP_OAuth::buildHTTPQuery($this->getParameters()));
        }
    }

    /**
     * Call
     *
     * Helps wrap self::$message (HttpMessage) to pass method calls to it
     *
     * @param string $method Method to call
     * @param array  $args   Arguments for the method being called
     *
     * @return mixed Result of the method
     * @throws BadMethodCallException Upon method not existing on self::$message
     */
    public function __call($method, $args)
    {
        static $prepareBodyFor = array('send', 'toString');
        if (in_array($method, $prepareBodyFor)) {
            $this->prepareBody();
        }

        if (method_exists($this->message, $method)) {
            return call_user_func_array(array($this->message, $method), $args);
        }

        throw new BadMethodCallException;
    }

}

?>
