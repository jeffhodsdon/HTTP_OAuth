<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth.php';
                                                                                
class HTTP_OAuthTest extends PHPUnit_Framework_TestCase        
{
    public function testEncode()
    {
        $raw = 'http://www.joestump.net/~foobar';
        $exp = 'http%3A%2F%2Fwww.joestump.net%2F~foobar';
        $bad = 'http%3A%2F%2Fwww.joestump.net%2F%7Efoobar';
        $res = HTTP_OAuth::encode($raw);
        
        $this->assertEquals($exp, $res);
        $this->assertNotEquals($bad, $res);
    }
}

?>
