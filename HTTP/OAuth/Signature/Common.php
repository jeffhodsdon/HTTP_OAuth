<?php

require_once 'HTTP/OAuth.php';

abstract class HTTP_OAuth_Signature_Common
{

    protected function getKey(array $secrets)
    {
        return implode('&', HTTP_OAuth::urlencode($secrets));
    }

    abstract public function build($string, array $secrets);

}

?>
