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

require_once 'HTTP/OAuth/Exception.php';
require_once 'HTTP/OAuth/Consumer/Response.php';

/**
 * HTTP_OAuth_Consumer_Exception_InvalidResponse
 *
 * Exception for invalid responses from OAuth providers. Supplies methods
 * to get information about the request
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */
class HTTP_OAuth_Consumer_Exception_InvalidResponse extends HTTP_OAuth_Exception
{

    /**
     * HTTP_OAuth_Consumer_Response instance
     *
     * @var HTTP_OAuth_Consumer_Response $resonse Invalid response
     */
    public $response = null;

    /**
     * Construct
     *
     * @param string                       $message  Exception message
     * @param HTTP_OAuth_Consumer_Response $response Invalid response
     *
     * @return void
     */
    public function __construct($message, HTTP_OAuth_Consumer_Response $response)
    {
        parent::__construct($message);

        $this->response = $response;
    }

    /**
     * Gets response body
     *
     * @return string Body of the response
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Gets raw request message
     *
     * @return string Raw request message
     */
    public function getRawRequest()
    {
        return $this->response->getRawRequestMessage();
    }

    /**
     * Gets raw response message
     *
     * @return string Raw response message
     */
    public function getRawResponse()
    {
        return $this->response->getRawResponseMessage();
    }

}

?>
