<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth.php';

class HTTP_OAuthTest extends PHPUnit_Framework_TestCase
{

    public function testBuildHTTPQuery()
    {
        $array = array(
            'oauth_consumer_key'     => 'e1nTvIGVCPkbfqZdIE7OyA',
            'oauth_token'            => 'kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_signature'        => '6WvHOHROOBkKcP3YrpnEHNbn1y4=',
            'oauth_timestamp'        => '1245711961',
            'oauth_nonce'            => 'EF35F352-6FB0-4CFD-98E2-136BC6507434',
            'oauth_version'          => '1.0'
        );

        $result = HTTP_OAuth::buildHTTPQuery($array);
        $this->assertEquals($result, 'oauth_consumer_key=e1nTvIGVCPkbfqZdIE7OyA&oauth_nonce=EF35F352-6FB0-4CFD-98E2-136BC6507434&oauth_signature=6WvHOHROOBkKcP3YrpnEHNbn1y4%3D&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1245711961&oauth_token=kRmeTe0wvuIJrIUbjoOfc4UZcUerJKR67BfXy20UM&oauth_version=1.0');
    }

    public function testURLEncode()
    {
        $raw = 'http://www.joestump.net/~foobar';
        $exp = 'http%3A%2F%2Fwww.joestump.net%2F~foobar';
        $bad = 'http%3A%2F%2Fwww.joestump.net%2F%7Efoobar';
        $res = HTTP_OAuth::urlencode($raw);
        
        $this->assertEquals($exp, $res);
        $this->assertNotEquals($bad, $res);
    }

    public function testURLDecode()
    {
        $raw = 'http://www.joestump.net/~foobar';
        $exp = 'http%3A%2F%2Fwww.joestump.net%2F~foobar';
        $res = HTTP_OAuth::urldecode($exp);

        $this->assertEquals($res, $raw);
    }
}

?>
