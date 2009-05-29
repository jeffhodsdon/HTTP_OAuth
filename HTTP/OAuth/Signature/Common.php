<?php

require_once 'HTTP/OAuth.php';

abstract class HTTP_OAuth_Signature_Common
{
    protected function normalizeParameters(array $parameters)
    {
        ksort($parameters);

        $sets = array();
        foreach ($key => $val) {
            $sets[] = $key . '=' . HTTP_OAuth::encode($val);
        }

        return implode('&', $sets);
    }

    protected function signatureBase()
    {

    }
}

?>
