<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth.php';
                                                                                
class HTTP_OAuthTest extends PHPUnit_Framework_TestCase        
{
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
