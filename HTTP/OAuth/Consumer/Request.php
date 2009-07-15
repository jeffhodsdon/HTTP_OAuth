<?php

require_once 'Validate.php';
require_once 'HTTP/OAuth/Message.php';
require_once 'HTTP/OAuth/Consumer/Response.php';
require_once 'HTTP/OAuth/Signature.php';
require_once 'HTTP/OAuth/Exception.php';

class HTTP_OAuth_Consumer_Request extends HTTP_OAuth_Message
{

    const AUTH_HEADER = 'header';

    const AUTH_POST = 'post';

    const AUTH_GET = 'get';

    protected $authType = 'header';

    protected $url = null;

    protected $method = null;

    protected $secrets = array();

    public function __construct($url, array $secrets, $method = 'POST')
    {
        $this->setUrl($url);
        $this->method  = $method;
        $this->secrets = $secrets;
    }

    public function setUrl($url)
    {
        if (!Validate::uri($url)) {
            throw new HTTP_OAuth_Exception("Invalid url: $url");
        }

        $this->url = $url;
    }

    public function getSecrets()
    {
        return $this->secrets;
    }

    public function setAuthType($type)
    {
        $this->authType = $type;
    }

    public function getAuthType()
    {
        return $this->authType;
    }

    public function send()
    {
        $request = $this->buildRequest();
        try {
            $response = $request->send();
        } catch (Exception $e) {
            throw new HTTP_OAuth_Exception('failed sending request');
        }

        return new HTTP_OAuth_Consumer_Response($response);
    }

    protected function buildRequest()
    {
        $this->oauth_timestamp = time();
        $this->oauth_nonce     = md5(microtime(true) . rand(1, 999));
        $this->oauth_version   = '1.0';

        $sig = HTTP_OAuth_Signature::factory($this->getSignatureMethod()); 
        $this->oauth_signature = $sig->build(
            $this->getRequestMethod(),
            $this->getUrl(),
            $this->getParameters(),
            $this->secrets[0],
            $this->secrets[1]
        );

        $method = HttpRequest::METH_POST;
        if ($this->method == 'GET') {
            $method = HttpRequest::METH_GET;
        }

        $request = new HttpRequest($this->url, $method);
        $request->addHeaders(array('Expect' => ''));
        $params = $this->getOAuthParameters();
        switch ($this->getAuthType()) {
            case self::AUTH_HEADER:
                $auth = $this->getAuthForHeader($params);
                $request->addHeaders(array('Authorization' => $auth));
                break;
            case self::AUTH_POST:
                $request->addPostData(HTTP_OAuth::urlencode($params));
                break;
            case self::AUTH_GET:
                $request->addQueryData(HTTP_OAuth::urlencode($params));
                break;
            default:
                throw new HTTP_OAuth_Exception;
                break;
        }

        return $request;
    }

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

    public function getRequestMethod()
    {
        return $this->method;
    }

    public function getUrl()
    {
        return $this->url;
    }

}

?>
