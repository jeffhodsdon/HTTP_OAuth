<?php

chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);    
                                                                                
require_once dirname(__FILE__) . '/HTTP/OAuthTest.php';        
require_once 'PHPUnit/Framework/TestSuite.php';                                 
                                                                                
class HTTP_OAuth_AllTests
{                                                                               
    public static function suite()                                              
    {   
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('HTTP_OAuthTest');                
        return $suite;
    }
}

?>
