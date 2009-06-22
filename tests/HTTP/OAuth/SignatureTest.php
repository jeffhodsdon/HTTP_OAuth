<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Signature.php';

class HTTP_OAuth_SignatureTest extends PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        $instance = HTTP_OAuth_Signature::factory('HMAC-SHA1');
        $this->assertType('HTTP_OAuth_Signature_HMAC_SHA1', $instance);
    }

}

?>
