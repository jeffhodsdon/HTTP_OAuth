<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'tests/HTTP/OAuth/MessageMock.php';

class HTTP_OAuth_MessageTest extends PHPUnit_Framework_TestCase
{

    public function testGetOAuthParameters()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m['token']        = 'foo';
        $m['consumer_key'] = 'key';
        $m['w000t']        = 'w000t';

        $p = $m->getOAuthParameters();
        $this->assertEquals(
            $p, array('oauth_consumer_key' => 'key', 'oauth_token' => 'foo')
        );
    }

    public function testSetParameters()
    {
        $p = array(
            'oauth_token'        => 'foo',
            'oauth_consumer_key' => 'key',
            'bar'          => 'w00t'
        );

        $m = new HTTP_OAuth_MessageMock;
        $m->setParameters($p);
        $this->assertEquals($m->getParameters(), $p);
    }

    public function testGetSignatureMethod()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m->signature_method = 'HMAC-SHA1';
        $this->assertEquals($m->getSignatureMethod(), 'HMAC-SHA1');
    }

    public function testMagicGetter()
    {
        $m = new HTTP_OAuth_MessageMock;
        $m->signature_method = 'HMAC-SHA1';
        $this->assertEquals($m->signatureMethod, 'HMAC-SHA1');
 
    }

}

?>
