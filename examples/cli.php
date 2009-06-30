<?php

chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

$base = realpath(dirname(__FILE__) . '/../../') . '/';
set_include_path("{$base}HTTP_OAuth:{$base}HTTP_OAuth_Consumer:{$base}HTTP_OAuth_Provider:" . get_include_path());

define(
    'USAGE',
    "Usage: php cli.php {method} [-{option}={value}, -{option}={value}, ...]\n"
);

class Config
{
    private $config = array();

    public function __get($var)
    {
        if (!array_key_exists($var, $this->config)) {
            echo "Missing {$var} option\n";
            echo USAGE;
            die(0);
        }

        return $this->config[$var];
    }

    public function __set($var, $val)
    {
        $this->config[$var] = $val;
    }
}

if (count($_SERVER['argv']) < 2) {
    echo USAGE;
    die(0);
}

$args = $_SERVER['argv'];
array_shift($args);
$method = array_shift($args);

$config = new Config;
foreach ($args as $arg) {
    list($name, $value) = explode('=', trim($arg, '-'));
    $config->$name = $value;
}

include $method . '.php';

?>
