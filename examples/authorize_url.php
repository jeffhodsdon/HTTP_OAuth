<?php

include_once 'HTTP/OAuth/Consumer.php';

$consumer = new HTTP_OAuth_Consumer(
    $config->consumer_key,
    $config->consumer_secret,
    $config->token,
    $config->token_secret
);

try {
    echo "Authorize URL: {$consumer->getAuthorizeUrl($config->authorize_url)}\n";
} catch (HTTP_OAuth_Consumer_Exception_InvalidResponse $e) {
    echo $e->getBody();
}

?>
