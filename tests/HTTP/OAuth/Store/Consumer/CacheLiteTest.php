<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'HTTP/OAuth/Store/Consumer/CacheLite.php';
require_once 'HTTP/OAuth/Store/Data.php';

class HTTP_OAuth_Store_Consumer_CacheLiteTest extends PHPUnit_Framework_TestCase
{
    protected $cache = null;

    protected function getDirectoryName()
    {
        return '/tmp/' . __CLASS__ . '/';
    }

    public function setUp()
    {
        $options = array('cacheDir' => $this->getDirectoryName());
        $this->cache = new HTTP_OAuth_Store_Consumer_CacheLite($options);
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . $this->getDirectoryName());
        $this->cache = null;
    }

    public function testSetGetRequestToken()
    {
        $token        = '12345';
        $tokenSecret  = '12345-secret';
        $providerName = 'acme';
        $sessionID    = '2112';
        

        $this->assertTrue($this->cache->setRequestToken($token, $tokenSecret, $providerName, $sessionID));
        $results = $this->cache->getRequestToken($providerName, $sessionID);
        $this->assertSame($token, $results['token']);
        $this->assertSame($tokenSecret, $results['tokenSecret']);
        $this->assertSame($providerName, $results['providerName']);
        $this->assertSame($sessionID, $results['sessionID']);
    }

    public function testSetGetRemoveAccessToken()
    {
        $data = new HTTP_OAuth_Store_Data();
        $data->consumerUserID    = 'consumer-username';
        $data->providerUserID    = 'provider-username';
        $data->providerName      = 'acme';
        $data->accessToken       = '12345';
        $data->accessTokenSecret = '12345-secret';
        $data->scope             = 'foo';

        $this->assertTrue($this->cache->setAccessToken($data));
        $results = $this->cache->getAccessToken($data->consumerUserID, $data->providerName);

        $this->assertInstanceOf('HTTP_OAuth_Store_Data', $results);

        $this->assertSame($data->consumerUserID, $results->consumerUserID);
        $this->assertSame($data->providerUserID, $results->providerUserID);
        $this->assertSame($data->providerName, $results->providerName);
        $this->assertSame($data->accessToken, $results->accessToken);
        $this->assertSame($data->accessTokenSecret, $results->accessTokenSecret);
        $this->assertSame($data->scope, $results->scope);

        $this->assertTrue($this->cache->removeAccessToken($data));
        $this->assertFalse($this->cache->getAccessToken($data->consumerUserID, $data->providerName));
    }
}
