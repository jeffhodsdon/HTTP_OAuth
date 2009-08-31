<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Provider/Response.php';

class HTTP_OAuth_Provider_ResponseTest extends PHPUnit_Framework_TestCase
{

    public function testSetStatus()
    {
        $ref = new ReflectionClass('HTTP_OAuth_Provider_Response');
        $statuses = array();
        foreach ($ref->getConstants() as $name => $val) {
            if (substr($name, 0, 7) !== 'STATUS_') {
                continue;
            }

            $res = $this->mockedResponse();
            $res->setStatus($val);
            $this->assertTrue((strlen($res->getBody()) > 1));
        }
    }

    /**
     * @expectedException HTTP_OAuth_Exception
     */
    public function testHeadersSentSetStatus()
    {
        $res = $this->mockedResponse();
        $res->expects($this->any())->method('headersSent')
            ->will($this->returnValue(true));
        $res->setStatus(HTTP_OAuth_Provider_Response::STATUS_UNSUPPORTED_PARAMETER);
    }

    /**
     * @expectedException HTTP_OAuth_Exception
     */
    public function testInvalidStatus()
    {
        $res = $this->mockedResponse();
        $res->setStatus(69);
    }

    public function testSetRealm()
    {
        $res = $this->mockedResponse();
        $res->setRealm('Digg OAuth');
        $headers = $res->getHeaders();
        $this->assertArrayHasKey('WWW-Authenticate', $headers);
        $this->assertEquals($headers['WWW-Authenticate'], 'OAuth realm="Digg OAuth"');
    }

    public function testGetHeader()
    {
        $res = $this->mockedResponse();
        $res->setHeader('Content-Type', 'foo');
        $this->assertEquals('foo', $res->getHeader('Content-Type'));
        $this->assertNull($res->getHeader('doesnotexist'));

        $res->setHeaders(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $res->getHeaders());
    }

    public function testSend()
    {
        $res = $this->mockedResponse();
        $res->token = 'foo';

        ob_start();
        $res->send();
        $output = ob_get_clean();
        $this->assertEquals('oauth_token=foo', $output);

        $res->expects($this->any())->method('headersSent')
            ->will($this->returnValue(true));
 
        ob_start();
        $res->send();
        $output = ob_get_clean();
        $this->assertEquals('oauth_token=foo', $output);
 
    }

    protected function mockedResponse(array $methods = array())
    {
        $methods = array_unique(array_merge($methods,
            array('headersSent', 'header')));
        $res = $this->getMock('HTTP_OAuth_Provider_Response', $methods);
        return $res;
    }

}

?>
