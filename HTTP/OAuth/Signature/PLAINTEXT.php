<?php

require_once 'HTTP/OAuth/Signature/Common.php';

class HTTP_OAuth_Signature_Plaintext extends HTTP_OAuth_Signature_Common
{

    public function build($string, array $secrets)
    {
        return $this->getKey();
    }

}

?>
