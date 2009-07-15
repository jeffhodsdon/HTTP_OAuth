<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Provider/Response.php';

class HTTP_OAuth_Provider_ResponseTest extends PHPUnit_Framework_TestCase
{

    public function testAuthenticateHeader()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $headers = $res->getHeaders();
        $this->assertArrayHasKey('WWW-Authenticate', $headers);
        $this->assertEquals($headers['WWW-Authenticate'], 'OAuth');
    }

    public function testSetStatus()
    {
        $ref = new ReflectionClass('HTTP_OAuth_Provider_Response');
        $statuses = array();
        foreach ($ref->getConstants() as $name => $val) {
            if (substr($name, 0, 7) !== 'STATUS_') {
                continue;
            }

            $res = new HTTP_OAuth_Provider_Response;
            $res->setStatus($val);
            $this->assertNotEquals($res->getResponseCode(), 200);
            $this->assertTrue((strlen($res->getBody()) > 1));
        }
    }

    /**
     * @expectedException HTTP_OAuth_Exception
     */
    public function testInvalidStatus()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $res->setStatus(69);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testBadMethodCall()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $res->methodThatShouldNotExistLOL();
    }

    public function testGetMessage()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $this->assertType('HttpMessage', $res->getMessage());
    }

    public function testSetRealm()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $res->setRealm('Digg OAuth');
        $headers = $res->getHeaders();
        $this->assertArrayHasKey('WWW-Authenticate', $headers);
        $this->assertEquals($headers['WWW-Authenticate'], 'OAuth realm="Digg OAuth"');
    }

    public function testToString()
    {
        $res = new HTTP_OAuth_Provider_Response;
        $res->setRealm('Digg OAuth');
        $res['token']  = md5('jeff rules');
        $res['secret'] = md5('pizza is the best');
        $string = "HTTP/1.1 200 Ok\r\nWWW-Authenticate: OAuth realm=\"Digg OAuth\"\r\n\r\noauth_token=52512b016323420ea8afdf8a02066657&secret=55aa35ebd6d5f4e4162f5cacf63e0b61\r\n";
        $this->assertEquals($string, $res->toString());
    }

}

?>
