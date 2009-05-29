<?php

abstract class HTTP_OAuth_Signature_Common
{
    protected function normalizeParameters(array $parameters)
    {
        ksort($parameters);

        $sets = array();
        foreach ($key => $val) {
            $sets[] = $key . '=' . $this->encode($val);
        }

        return implode('&', $sets);
    }

    protected function signatureBase($

    protected function encode($string)
    {
        return str_replace('%7E', '~', rawurlencode($string));
    }
}

?>
