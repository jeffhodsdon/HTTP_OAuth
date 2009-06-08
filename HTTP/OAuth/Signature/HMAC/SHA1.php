<?php

require_once 'HTTP/OAuth/Signature/Common.php';

class HTTP_OAuth_Signature_HMAC_SHA1 extends HTTP_OAuth_Signature_Common
{

    public function build($string, array $secrets)
    {
        return base64_encode(
            hash_hmac('sha1', $string, $this->getKey($secrets), true)
        );
    }

}

?>
