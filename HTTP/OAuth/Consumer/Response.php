<?php

require_once 'HTTP/OAuth/Message.php';
require_once 'HTTP/OAuth/Exception.php';

class HTTP_OAuth_Consumer_Response extends HTTP_OAuth_Message
{
    protected $message = null;

    public function __construct(HttpMessage $message)
    {
        $this->message = $message;
    }

    public function getDataFromBody()
    {
        $result = array();
        parse_str($this->message->getBody(), $result);
        return $result;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->message, $method)) {
            return call_user_func_array(array($this->message, $method), $args);
        }

        throw new BadMethodCallException($method);
    }

}

?>
