<?php

chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

$base = realpath(dirname(__FILE__) . '/../../') . '/';
set_include_path("{$base}HTTP_OAuth:{$base}HTTP_OAuth_Consumer:{$base}HTTP_OAuth_Provider:" . get_include_path());

?>

<style type="text/css" media="all">@import "style.css";</style>

<script src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#fetch_request_token').click(function() {
        $.ajax({
            type: "GET",
            url: "request_token.php",
            dataType: "json",
            data: {
                consumer_key: $('#consumer_key').value,
                consumer_secret: $('#consumer_secret').value,
                request_token_url: $('#request_token_url').value
            },
            success: function(json) {
                if (json.error !== null ||
                    (json.token === null || json.token_secret === null)
                ) {
                    alert((json.error) ? json.error : 'Error!');
                    return false;
                }

                
            }
        });

        return false;
    });

});
</script>

<div class="settings">
    <h2>Settings</h2>
    <div class="setting">
        <label for="">Consumer Key</label>
        <input type="text" value="" id="consumer_key">
    </div>
    <div class="setting">
        <label for="">Consumer Secret</label>
        <input type="text" value="" id="consumer_secret">
    </div>
    <div class="setting">
        <label for="">Request Token URL</label>
        <input type="text" value="" id="">
    </div>
    <div class="setting">
        <label for="">Authorize URL</label>
        <input type="text" value="" id="authorize_url">
    </div>
    <div class="setting">
        <label for="">Access Token URL</label>
        <input type="text" value="" id="">
    </div>
    <div class="setting">
        <label for="">Token</label>
        <input type="text" value="" id="" readonly>
    </div>
    <div class="setting">
        <label for="">Token Secret</label>
        <input type="text" value="" id="" readonly>
    </div>
    <div class="setting">
        <label for="">Verifier</label>
        <input type="text" value="" id="">
    </div>
</div>

<div class="example">
    <h2>OAuth Example</h2>
    <div class="step">
        <span><a id="fetch_request_token">Fetch request token</a></span>
        <div><i>Not yet sent</i></div>
    </div>
    <div class="step">
        <span><a href="">Open authorize url</a></span>
        <div></div>
    </div>
    <div class="step">
        <span><a id="fetch_access_token">Fetch access token</a></span>
        <div><i>Not yet sent</i></div>
    </div>
</div>
