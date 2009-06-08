<?php

class HTTP_OAuth
{

    static public function buildHttpQuery(array $params)
    {
        if (empty($params)) {
            return '';
        }

        $keys   = self::urlencode(array_keys($params));
        $values = self::urlencode(array_values($params));
        $params = array_combine($keys, $values);

        uksort($params, 'strcmp');

        $pairs = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                natsort($value);
                foreach ($value as $dupe) {
                    $pairs[] = $key . '=' . $dupe;
                }

                continue;
            }

            $pairs[] =  $key . '=' . $value;
        }

        return implode('&', $pairs);
    }

    static public function urlencode($item)
    {
        static $search  = array('+', '%7E');
        static $replace = array(' ', '~');

        if (is_array($item)) {
            return array_map(array('HTTP_OAuth', 'urlencode'), $item);
        }

        if (is_scalar($item) === false) {
            throw new HTTP_OAuth_Exception;
        }

        return str_replace($search, $replace, rawurldecode($item));
    }

    static public function urldecode($string)
    {
        return urldecode($string);
    }

}

?>
