# HTTP_OAuth - Implementation of the OAuth specification

HTTP_OAuth is a PEAR package implementing the OAuth 1.0a protocol.
Consumer, Provier (request and response) classes are provided.
See the Consumer examples below:


## HTTP_OAuth_Consumer

Main consumer class that assists consumers in establishing OAuth
creditials and making OAuth requests.

### Example:

$consumer = new HTTP_OAuth_Consumer('key', 'secret');
$consumer->getRequestToken('http://example.com/oauth/request_token', $callback);

// Store tokens
$_SESSION['token']        = $consumer->getToken();
$_SESSION['token_secret'] = $consumer->getTokenSecret();

$url = $consumer->getAuthorizeUrl('http://example.com/oauth/authorize');
http_redirect($url); // function from pecl_http

// When they come back via the $callback url
$consumer = new HTTP_OAuth_Consumer('key', 'secret', $_SESSION['token'],
    $_SESSION['token_secret']);
$consumer->getAccessToken('http://example.com/oauth/access_token');

// Store tokens
$_SESSION['token']        = $consumer->getToken();
$_SESSION['token_secret'] = $consumer->getTokenSecret();

// $response is an instance of HTTP_OAuth_Consumer_Response
$response = $consumer->sendRequest('http://example.com/oauth/protected_resource');
