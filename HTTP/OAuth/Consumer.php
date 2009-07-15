<?php
/**
 * HTTP_OAuth_Consumer 
 * 
 * @package HTTP_OAuth
 * @author  Jeff Hodsdon <jeff@digg.com> 
 */

require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Consumer/Request.php';
require_once 'HTTP/OAuth/Consumer/Exception/InvalidResponse.php';

/**
 * HTTP_OAuth_Consumer 
 * 
 * @package HTTP_OAuth
 * @author  Jeff Hodsdon <jeff@digg.com> 
 */
class HTTP_OAuth_Consumer
{

    /**
     * Consumer key 
     * 
     * @var string $key Consumer key
     */
    protected $key = null;

    /**
     * secret 
     * 
     * @var string $secret Consumer secret
     */
    protected $secret = null;

    /**
     * Token 
     * 
     * @var string Access/Request token
     */
    protected $token = null;

    /**
     * Token secret 
     * 
     * @var string $tokenSecret Access/Request token secret
     */
    protected $tokenSecret = null;

    /**
     * Signature method 
     * 
     * @var string $signatureMethod Signature method
     */
    protected $signatureMethod = 'HMAC-SHA1';

    /**
     * Construct 
     * 
     * @param string $key          Consumer key
     * @param string $secret       Consumer secret
     * @param string $token        Access/Reqest token
     * @param string $tokenSecret  Access/Reqest token secret
     *
     * @return void
     */
    public function __construct($key, $secret, $token = null, $tokenSecret = null)
    {
        $this->key    = $key;
        $this->secret = $secret;
        $this->setToken($token);
        $this->setTokenSecret($tokenSecret);
    }

    /**
     * Get request token 
     * 
     * @param string $url        Request token url
     * @param array  $additional Additional parameters to be in the request
     * @param string $method     HTTP request method. POST is default and
     *                           recommended in the spec.
     *
     * @return void
     */
    public function getRequestToken($url, $callback = 'oob', array $additional = array())
    {
        $additional['oauth_callback'] = $callback;
        $res  = $this->sendRequest($url, $additional);
        $data = $res->getDataFromBody();
        if (empty($data['oauth_token']) || empty($data['oauth_token_secret'])) {
            throw new HTTP_OAuth_Consumer_Exception_InvalidResponse(
                'Failed getting token and token secret from response', $res
            );
        }

        $this->setToken($data['oauth_token']);
        $this->setTokenSecret($data['oauth_token_secret']);
    }

    /**
     * Get access token 
     * 
     * @param string $url    Access token url 
     * @param string $method HTTP request method. POST is default and
     *                       recommended in the spec.
     *
     * @return array Token and token secret
     */
    public function getAccessToken($url, $verifier = '')
    {
        $res  = $this->sendRequest($url, array('oauth_verifier' => $verifier));
        $data = $res->getDataFromBody();
        if (empty($data['oauth_token']) || empty($data['oauth_token_secret'])) {
            throw new HTTP_OAuth_Consumer_Exception_InvalidResponse(
                'Failed getting token and token secret from response', $res
            );
        }

        $this->setToken($data['oauth_token']);
        $this->setTokenSecret($data['oauth_token_secret']);
    }

    /**
     * Get authorize url 
     * 
     * @param string $url        Authorization url
     * @param array  $additional Additional parameters for the auth url
     *
     * @return string Authorization url
     */
    public function getAuthorizeUrl($url, array $additional = array())
    {
        $params = array('oauth_token' => $this->getToken());
        $params = array_merge($additional, $params);

        return sprintf('%s?%s', $url, HTTP_OAuth::buildHTTPQuery($params));
    }

    public function sendRequest($url, array $additional = array(), $method = 'POST')
    {
        $params = array(
            'oauth_consumer_key'     => $this->key,
            'oauth_signature_method' => $this->getSignatureMethod()
        );

        if ($this->getToken()) {
            $params['oauth_token'] = $this->getToken();
        }

        $params = array_merge($additional, $params);

        $req = new HTTP_OAuth_Consumer_Request($url, $this->getSecrets(), $method);
        $req->setParameters($params);
        return $req->send();
    }

    /**
     * Get key
     *
     * @return string Consumer key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get secret
     *
     * @return string Consumer secret
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get token 
     * 
     * @return string Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token 
     * 
     * @param string $token Request/Access token
     *
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token secret
     * 
     * @return string Accessoken secret
     */
    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }

    /**
     * Set token secret 
     * 
     * @param string $secret Token secret
     *
     * @return void
     */
    public function setTokenSecret($secret)
    {
        $this->tokenSecret = $secret;
    }

    /**
     * Get signature method 
     *
     * @return string Signature method
     */
    public function getSignatureMethod()
    {
        return $this->signatureMethod;
    }

    /**
     * Set signature method 
     * 
     * @param string $method Signature method to use
     *
     * @return void
     */
    public function setSignatureMethod($method)
    {
        $this->signatureMethod = $method;
    }

    /**
     * Get secrets 
     * 
     * @return array Array possible secrets
     */
    protected function getSecrets()
    {
        return array($this->secret, (string) $this->tokenSecret);
    }

}

?>
