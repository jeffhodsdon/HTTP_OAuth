<?php

require_once 'HTTP/OAuth/Signature/Common.php';
require_once 'HTTP/OAuth/Exception/NotImpemented.php';

class HTTP_OAuth_Signature_RSA_SHA1
{

    public function build($string, array $secrets)
    {
        throw new HTTP_OAuth_Exception_NotImplemented;
    }

}

?>
