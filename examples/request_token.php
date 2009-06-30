<?php

include_once 'HTTP/OAuth/Consumer.php';

$consumer = new HTTP_OAuth_Consumer(
    $config->consumer_key, $config->consumer_secret
);

try {
    $consumer->getRequestToken($config->request_token_url);
    echo "Request Token: {$consumer->getToken()}\n";
    echo "Request Token Secret: {$consumer->getTokenSecret()}\n";
} catch (HTTP_OAuth_Consumer_Exception_InvalidResponse $e) {
    echo $e->getBody();
}

?>
