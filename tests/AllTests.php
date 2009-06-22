<?php

chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once dirname(__FILE__) . '/HTTP/OAuthTest.php';
require_once dirname(__FILE__) . '/HTTP/OAuth/Signature/HMAC/SHA1Test.php';
require_once 'PHPUnit/Framework/TestSuite.php';                                 
                                                                                
class HTTP_OAuth_AllTests
{                                                                               
    public static function suite()                                              
    {   
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('HTTP_OAuthTest');                
        $suite->addTestSuite('HTTP_OAuth_Signature_HMAC_SHA1Test');
        return $suite;
    }
}

?>
