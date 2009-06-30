<?php
/**
 * HTTP_OAuth
 *
 * Implementation of the OAuth specification
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive  
 * a copy of the New BSD License and are unable to obtain it through the web, 
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth_Provider
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth_Provider
 */

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
