<?php

class HTTP_OAuth
{
    /**
     * RFC 3986 compliant encode method
     *
     * OAuth requires that values be encoded according to RFC 3986. Until PHP
     * 5.3 is widely available, this hack is required.
     *
     * @param string $input The string to encode
     *
     * @link http://www.ietf.org/rfc/rfc3986.txt
     * @return string
     */
    static public function encode($input) 
    {
        return str_replace('%7E', '~', rawurlencode($input));
    }

    /**
     * Complimentary decode method
     *
     * @param string $input The string to encode
     *
     * @link http://www.ietf.org/rfc/rfc3986.txt
     * @return string
     */
    static public function decode($input)
    {
        return rawurldecode($input);
    }
}

?>
