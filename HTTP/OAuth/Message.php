<?php
/**
 * HTTP_OAuth_Message
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

/**
 * HTTP_OAuth_Message
 * 
 * @category  HTTP
 * @package   HTTP_OAuth
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class HTTP_OAuth_Message implements ArrayAccess
{

    /**
     * OAuth Parameters 
     *
     * @var string $oauthParams OAuth parameters
     */
    static protected $oauthParams = array(
        'oauth_consumer_key',
        'oauth_token',
        'oauth_token_secret',
        'oauth_signature_method',
        'oauth_signature',
        'oauth_timestamp',
        'oauth_nonce',
        'oauth_version',
        'oauth_callback'
    );

    /**
     * Parameters 
     * 
     * @var array $parameters Parameters
     */
    protected $parameters = array();

    /**
     * Get OAuth Parameters 
     * 
     * @return array OAuth specific parameters
     */
    public function getOAuthParameters()
    {
        $params = array();
        foreach (self::$oauthParams as $param) {
            if ($this->$param !== null) {
                $params[$param] = $this->$param;
            }
        }

        ksort($params);

        return $params;
    }

    /**
     * Get parameters 
     * 
     * @return array Request's parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set parameters 
     * 
     * @param array $params Name => value pair array of parameters
     *
     * @return void
     */
    public function setParameters(array $params)
    {
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Get signature method 
     * 
     * @return string Signature method
     */
    public function getSignatureMethod()
    {
        return $this->oauth_signature_method;
    }

    /**
     * Get 
     * 
     * @param string $var Variable to get
     *
     * @return mixed Parameter if exists, else null
     */
    public function __get($var)
    {
        if (array_key_exists($var, $this->parameters)) {
            return $this->parameters[$var];
        }

        $method = 'get' . ucfirst($var);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * Set 
     * 
     * @param string $var Name of the variable
     * @param mixed  $val Value of the variable
     *
     * @return void
     */
    public function __set($var, $val)
    {
        $this->parameters[$var] = $val;
    }

    /**
     * Offset exists 
     * 
     * @param string $offset Name of the offset
     *
     * @return bool Offset exists or not
     */
    public function offsetExists($offset)
    {
        return isset($this->parameters[$offset]);
    }

    /**
     * Offset get 
     * 
     * @param string $offset Name of the offset
     *
     * @return string Offset value
     */
    public function offsetGet($offset)
    {
        return $this->parameters[$offset];
    }

    /**
     * Offset set 
     * 
     * @param string $offset Name of the offset
     * @param string $value  Value of the offset 
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->parameters[$offset] = $value;
    }

    /**
     * Offset unset 
     * 
     * @param string $offset Name of the offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->parameters[$offset]);
    }

}

?>
