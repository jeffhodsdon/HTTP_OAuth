<?php
/**
 * HTTP_OAuth
 *
 * Implementation of the OAuth specification
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive  
 * a copy of the New BSD License and are unable to obtain it through the web, 
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com> 
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth_Provider
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth_Provider
 */

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
