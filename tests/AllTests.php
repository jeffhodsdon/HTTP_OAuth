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

$path = realpath(dirname(__FILE__));
set_include_path($path . ':' . realpath($path . '/../') . ':' . get_include_path());

require_once 'HTTP/OAuthTest.php';
require_once 'HTTP/OAuth/MessageTest.php';
require_once 'HTTP/OAuth/ConsumerTest.php';
require_once 'HTTP/OAuth/Consumer/RequestTest.php';
require_once 'HTTP/OAuth/Consumer/ResponseTest.php';
require_once 'HTTP/OAuth/Consumer/Exception/InvalidResponseTest.php';
require_once 'HTTP/OAuth/SignatureTest.php';
require_once 'HTTP/OAuth/Signature/CommonTest.php';
require_once 'HTTP/OAuth/Signature/RSA/SHA1Test.php';
require_once 'HTTP/OAuth/Signature/HMAC/SHA1Test.php';
require_once 'HTTP/OAuth/Signature/PLAINTEXTTest.php';
require_once 'HTTP/OAuth/Provider/RequestTest.php';
require_once 'HTTP/OAuth/Provider/ResponseTest.php';
require_once 'HTTP/OAuth/Store/Consumer/CacheLiteTest.php';
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * HTTP_OAuth_AllTests 
 * 
 * Main test suite, includes all the tests in the top level HTTP_OAuth
 * package.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth_Provider
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth_Provider
 */
class AllTests
{
    /**
     * Suite 
     * 
     * Returns the whole suite for HTTP_OAuth
     *
     * @return mixed|PHPUnit_Framework_TestSuite Unit test suite
     */
    public static function suite()
    {   
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('HTTP_OAuthTest');                
        $suite->addTestSuite('HTTP_OAuth_MessageTest');
        $suite->addTestSuite('HTTP_OAuth_ConsumerTest');
        $suite->addTestSuite('HTTP_OAuth_Consumer_RequestTest');
        $suite->addTestSuite('HTTP_OAuth_Consumer_ResponseTest');
        $suite->addTestSuite('HTTP_OAuth_Consumer_Exception_InvalidResponseTest');
        $suite->addTestSuite('HTTP_OAuth_SignatureTest');
        $suite->addTestSuite('HTTP_OAuth_Signature_CommonTest');
        $suite->addTestSuite('HTTP_OAuth_Signature_PLAINTEXTTest');
        $suite->addTestSuite('HTTP_OAuth_Signature_HMAC_SHA1Test');
        $suite->addTestSuite('HTTP_OAuth_Signature_RSA_SHA1Test');
        $suite->addTestSuite('HTTP_OAuth_Provider_RequestTest');
        $suite->addTestSuite('HTTP_OAuth_Provider_ResponseTest');
        $suite->addTestSuite('HTTP_OAuth_Store_Consumer_CacheLiteTest');
 
        return $suite;
    }
}

?>
