<?php

interface HTTP_OAuth_Signature_Interface
{
    /**
     * Return a signature
     * 
     * @param array $parameters The parameters to use for signature
     *
     * @return string The signature for given parameters
     */
    public function sign(array $parameters, $method, $url);


    /**
     * Validate a signature
     *
     * @param array $parameters The parameters passed with signature
     *
     * @return boolean
     */
    public function validate(array $parameters, $method, $url);
}

?>
