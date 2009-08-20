<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Provider/Response.php';

class HTTP_OAuth_Provider_ResponseTest extends PHPUnit_Framework_TestCase
{

    public function testAuthenticateHeader()
    {
        $res = $this->mockedResponse();
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

            $res = $this->mockedResponse();
            $res->setStatus($val);
            $this->assertTrue((strlen($res->getBody()) > 1));
        }
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

    protected function mockedResponse()
    {
        $res = $this->getMock('HTTP_OAuth_Provider_Response',
            array('headersSent', 'header'));
        $res->expects($this->any())->method('headersSent')
            ->will($this->returnValue(false));
        return $res;
    }

}

?>
