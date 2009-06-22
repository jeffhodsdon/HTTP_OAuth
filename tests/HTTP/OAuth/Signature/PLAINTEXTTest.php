<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Signature/PLAINTEXT.php';
require_once 'HTTP/OAuth.php';

class HTTP_OAuth_Signature_PLAINTEXTTest extends PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        $signature = new HTTP_OAuth_Signature_PLAINTEXT;
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
            array('s85GLpyelma8rvNCgOjxi3lBXoedqsoDas6OYIQCeI', '')
        );

        $this->assertEquals(
            's85GLpyelma8rvNCgOjxi3lBXoedqsoDas6OYIQCeI%26',
            HTTP_OAuth::urlencode($result)
        );
    }

}

?>
