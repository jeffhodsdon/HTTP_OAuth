<?php

abstract class HTTP_OAuth_Signature
{
    static public function factory($method) 
    {
        $base = strtoupper(str_replace('-', '_', $method));
        $file = 'HTTP/OAuth/Signature/' . $base . '.php';

        include_once $file;

        $class = 'HTTP_OAuth_Signature_' . $base;
        if (!class_exists($class, false)) {
            throw new HTTP_OAuth_Exception(
                'Invalid/Missing signature class in ' . $file
            );
        }

        $instance = new $class();
        return $instance;
    }

    private function __construct()
    {

    }
}

?>
