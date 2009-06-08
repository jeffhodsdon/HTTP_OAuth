<?php

require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Request.php';

class HTTP_OAuth_Signature
{

    static public function sign($method, HTTP_OAuth_Request $request)
    {
        $parts = array(
            $request->getRequestMethod(),
            $request->getRequestUrl(),
            HTTP_OAuth::buildHttpQuery($request->getParameters())
        );

        $base = implode('&', $parts);

        if ($request->consumer_secret === null) {
            throw new HTTP_OAuth_Exception('Missing consumer_secret');
        }

        $secrets = array(
            $request->consumer_secret,
            ($request->token_secret === null) ? '' : $request->token_secret
        );

        $sig = self::factory($method)->build($base, $secrets);
        $request->sig = $sig;
    }

    static public function factory($method)
    {
        $class = 'HTTP_OAuth_Signature_' . $method;
        $file  = str_replace('_', '/', $class) . '.php';

        include_once $file;

        return new $class;
    }
}

?>
