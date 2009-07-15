<?php

require_once 'HTTP/OAuth/Exception.php';

class HTTP_OAuth_Consumer_Exception_InvalidResponse extends HTTP_OAuth_Exception
{

    public $response = null;

    public function __construct($message, HTTP_OAuth_Consumer_Response $response)
    {
        parent::__construct($message);

        $this->response = $response;
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function getRawRequest()
    {
        return $this->response->getRawRequestMessage();
    }

    public function getRawResponse()
    {
        return $this->response->getRawResponseMessage();
    }
}

?>
