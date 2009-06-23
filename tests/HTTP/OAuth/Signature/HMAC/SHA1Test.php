<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Signature/HMAC/SHA1.php';
require_once 'HTTP/OAuth.php';

class HTTP_OAuth_Signature_HMAC_SHA1Test extends PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        $signature = new HTTP_OAuth_Signature_HMAC_SHA1;
        $result = $signature->build(
            'POST',
            'http://twitter.com/oauth/request_token',
            array(
                'oauth_consumer_key' => 'e1nTvIGVCPkbfqZdIE7OyA',
                'oauth_nonce'       => '5319B2C4-92DD-4568-B34C-993C5A102B2D',
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => '1245709445',
                'oauth_version'   => '1.0'
            ),
            's85GLpyelma8rvNCgOjxi3lBXoedqsoDas6OYIQCeI'
        );

        $this->assertEquals(
            '6vdoM0LiiLr%2FjqcZqIE5Nq3I8Dc%3D',
            HTTP_OAuth::urlencode($result)
        );
    }

}

?>
