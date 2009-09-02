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
set_include_path("{$base}HTTP_OAuth:" . get_include_path());

require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Consumer/Request.php';
require_once 'HTTP/Request2.php';

//require_once 'Log.php';
//HTTP_OAuth::attachLog(Log::singleton('display'));

/* Configuration for Examples */

class Config
{
    private $config = array();

    public $isHttp = false;

    public function __get($var)
    {
        if (!array_key_exists($var, $this->config)) {
            if ($this->isHttp) {
                header('HTTP/1.1 500 Internal Server Error');
            }

            echo "Missing {$var} option\n";
            die(0);
        }

        return $this->config[$var];
    }

    public function __set($var, $val)
    {
        $this->config[$var] = $val;
    }
}

$config = new Config;
if (!empty($_GET)) {
    $config->isHttp = true;
    foreach ($_GET as $name => $val) {
        if (empty($val)) {
            continue;
        }

        if (is_array($val)) {
            foreach ($val as $n => $v) {
                if (!strlen($v)) {
                    unset($val[$n]);
                }
            }
        }

        $config->$name = $val;
    }
}

$httpRequest = new HTTP_Request2;
$httpRequest->setHeader('Accept-Encoding', '.*');
$request = new HTTP_OAuth_Consumer_Request;
$request->accept($httpRequest);

?>
