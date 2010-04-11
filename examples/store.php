<?php

session_start();
// ini_set('include_path', '/Users/bill/git/HTTP_OAuth:' . get_include_path());

require_once 'HTTP/OAuth/Consumer.php';
require_once 'HTTP/OAuth/Store/Consumer/CacheLite.php';

$key      = 'key';
$secret   = 'secret';
$callback = 'http://openid.local/oauth.php';
$store    = new HTTP_OAuth_Store_Consumer_CacheLite();

if (isset($_GET['start'])) {
    $consumer = new HTTP_OAuth_Consumer($key, $secret);
    $consumer->getRequestToken('http://twitter.com/oauth/request_token', $callback);
    $store->setRequestToken($consumer->getToken(), $consumer->getTokenSecret(), 'twitter', session_id());

    $url = $consumer->getAuthorizeUrl('http://twitter.com/oauth/authorize');
    header("Location: $url");
    exit;
} else if (count($_GET)) {
    $tokens   = $store->getRequestToken('twitter', session_id());
    $consumer = new HTTP_OAuth_Consumer($key, $secret, $tokens['token'], $tokens['tokenSecret']);

    // Verifier
    $verifier = null;
    $qsArray  = explode('?', $_SERVER['REQUEST_URI']);
    if (isset($qsArray[1])) {
        parse_str($qsArray[1], $parsed);
        if (isset($parsed['oauth_verifier'])) {
            $verifier = $parsed['oauth_verifier'];
        }
    }

    $consumer->getAccessToken('http://twitter.com/oauth/access_token', $verifier);
    $data = new HTTP_OAuth_Store_Data();
    $data->consumerUserID    = 'shupp';
    $data->providerUserID    = 'shupp';
    $data->providerName      = 'twitter';
    $data->accessToken       = $consumer->getToken();
    $data->accessTokenSecret = $consumer->getTokenSecret();

    $store->setAccessToken($data);

    $stored = $store->getAccessToken('shupp', 'twitter');
    var_dump($stored);
    exit;
}

echo "<a href='./oauth.php?start=true'>start!</a>";

// $response is an instance of HTTP_OAuth_Consumer_Response
// $response = $consumer->sendRequest('http://example.com/oauth/protected_resource');

?>
