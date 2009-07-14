<html>
<head>
    <title>HTTP_OAuth Example Tool</title>
    <style type="text/css" media="all">
    html {
        background: #ededed;
        font: 82.5% Helvetica Neue, HelveticaNeue, Helvetica, sans-serif;
    }

    .defaults {
        width: 160px;
        float: right;
    }
    .defaults img {
        border: 0;
    }

    h3 {
        float: left;
        margin-top: 13px;
        color: #444;
        float: left;
        font-size: 1em;
        line-height: 1;
        font-weight: 500;
        letter-spacing: -1px;

    }
    .default {
        float: right;
        margin-top: 10px;
        padding: 2px;
    }

    .settings, .example {
        width: 500px;
        margin: 60px 20px 20px 20px;
        padding: 20px 30px 30px 20px;
        background: #fff;
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
    }

    .settings h2, .example h2 {
        color: #444;
        width: 250px;
        float: left;
        font-size: 2.4em;
        line-height: 1;
        font-weight: ;
        letter-spacing: -1px;
        margin-top: 5px;
    }

    .setting label {

    font-size: .85em;
    font-weight: bold;
    color: #444;
    float: left;

    }

    .setting input {
        width: 350px;
        display: block;
        margin-bottom: 12px;
        margin-left:155px !important;
    }

    .example {
        margin-top: 10px !important;
        background: #f6f6f6;
    }

    .example a {
        display: block;
        text-decoration: none;
        padding: 10px 0;
        font-weight: bold;
        color: #0d90e4;
        border-bottom: 1px solid #ddd;

    }

    .example a:hover {
        color: #0066a7;
    }

    .clear {
        clear: both;
    }

    .code {
        display: none;
    }
    </style>

    <script src="jquery.js" type="text/javascript"></script>
    <script src="jquery.cookie.js" type="text/javascript"></script>
    <script src="jquery.fade.js" type="text/javascript"></script>
</head>
<body>

<script type="text/javascript">
$(document).ready(function() {
    $('input').each(function() {
        if (this.value.length < 1) {
            this.value = $.cookie(this.id);
        }
    });

    $('#fetch_request_token').click(function() {
        $.ajax({
            type: "GET",
            url: "request_token.php",
            dataType: "json",
            data: {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                request_token_url: $('#request_token_url').val(),
                callback_url: window.location.toString()
            },
            success: function(json) {
                if (json.token === undefined || json.token_secret === undefined) {
                    alert('Error!');
                }

                $('#token').val(json.token).vkfade();
                $('#token_secret').val(json.token_secret).vkfade();
                $('#error').html('');
                return false;
            },
            error: function(res) {
                $('#error').html(res.responseText).vkfade('#CC1100');
            }
        });

        return false;
    });

    $('#open_authorize_url').click(function() {
        var url = $('#authorize_url').val();
        if (url.length < 1) {
            alert('Missing authorize url setting');
            return false;
        }

        var token = $('#token').val();
        if (token.length < 1) {
            alert('Missing request token!');
        }

        $('input').each(function() {
            $.cookie(this.id, this.value);
        });

        window.location = url + '?oauth_token=' + token;
        return false;
    });

    $('#fetch_access_token').click(function() {
        $.ajax({
            type: "GET",
            url: "access_token.php",
            dataType: "json",
            data: {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                access_token_url: $('#access_token_url').val(),
                token: $('#token').val(),
                token_secret: $('#token_secret').val(),
                verifier: $('#verifier').val()
            },
            success: function(json) {
                if (json.token === undefined || json.token_secret === undefined) {
                    alert('Error!');
                }

                $('#token').val(json.token).vkfade();
                $('#token_secret').val(json.token_secret).vkfade();
                $('#error').html('');
                return false;
            },
            error: function(res) {
                $('#error').html(res.responseText).vkfade('#CC1100');
            }
        });

        return false;
    });

    $('#oauth_request').click(function() {
        $.ajax({
            type: "GET",
            url: "oauth_request.php",
            data: {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                protected_resource: $('#protected_resource').val(),
                token: $('#token').val(),
                token_secret: $('#token_secret').val()
            },
            success: function(res) {
                $('#success').html(res).vkfade();
                return false;
            },
            error: function(res) {
                $('#error').html(res.responseText).vkfade('#CC1100');
            }
        });

        return false;
    });

    $('#digg').click(function() {
        $('#request_token_url').val('http://services.digg.com/oauth/tokens/request');
        $('#authorize_url').val('http://digg.com/oauth/authorize');
        $('#access_token_url').val('http://services.digg.com/oauth/tokens/access');

        return false;
    });

    $('#twitter').click(function() {
        $('#request_token_url').val('http://twitter.com/oauth/request_token');
        $('#authorize_url').val('http://twitter.com/oauth/authorize');
        $('#access_token_url').val('http://twitter.com/oauth/access_token');
        $('#protected_resource').val('http://twitter.com/account/verify_credentials.xml');

        return false;
    });

    $('#google').click(function() {
        $('#request_token_url').val('https://www.google.com/accounts/OAuthGetRequestToken');
        $('#authorize_url').val('https://www.google.com/accounts/OAuthAuthorizeToken');
        $('#access_token_url').val('https://www.google.com/accounts/OAuthGetAccessToken');
        $('#protected_resource').val('http://www.google.com/calendar/feeds/default/allcalendars/full?orderby=starttime');

        return false;
    });

    $('#yahoo').click(function() {
        $('#request_token_url').val('http://api.login.yahoo.com/oauth/v2/get_request_token');
        $('#authorize_url').val('https://api.login.yahoo.com/oauth/v2/request_auth');
        $('#access_token_url').val('https://api.login.yahoo.com/oauth/v2/get_token');
        $('#protected_resource').val('http://social.yahooapis.com/v1/user/abcdef123/profile?format=json');

        return false;
    });

    $('#myspace').click(function() {
        $('#request_token_url').val('http://api.myspace.com/request_token');
        $('#authorize_url').val('http://api.myspace.com/authorize');
        $('#access_token_url').val('http://api.myspace.com/access_token');

        return false;
    });

    $('.show-code').click(function() {
        $(this).siblings('.code').toggle('fast');
        return false;
    });
});
</script>

<div class="settings">
    <h2>Settings</h2>
    <div class="defaults">
        <h3>Defaults: </h3>
        <div class="default">
            <a href="" id="myspace"><img src="myspace.jpg" /></a>
        </div>
        <div class="default">
            <a href="" id="yahoo"><img src="yahoo.gif" /></a>
        </div>
        <div class="default">
            <a href="" id="google"><img src="google.gif" /></a>
        </div>
        <div class="default">
            <a href="" id="twitter"><img src="twitter.gif" /></a>
        </div>
        <div class="default">
            <a href="" id="digg"><img src="digg.gif" /></a>
        </div>
    </div>
    <div class="clear"></div>

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
        <input type="text" value="" id="request_token_url">
    </div>
    <div class="setting">
        <label for="">Authorize URL</label>
        <input type="text" value="" id="authorize_url">
    </div>
    <div class="setting">
        <label for="">Access Token URL</label>
        <input type="text" value="" id="access_token_url">
    </div>
    <div class="setting">
        <label for="">Token</label>
        <input type="text" value="" id="token" readonly>
    </div>
    <div class="setting">
        <label for="">Token Secret</label>
        <input type="text" value="" id="token_secret" readonly>
    </div>
    <div class="setting">
        <label for="">Verifier</label>
        <input type="text" value="<?= @$_GET['oauth_verifier']; ?>" id="verifier">
    </div>
    <div class="setting">
        <label for="">Protected resource</label>
        <input type="text" value="" id="protected_resource">
    </div>
</div>

<div class="example">
    <h2>OAuth Example</h2>
    <div class="clear"></div>
    <div class="step">
        <span><a href="" id="fetch_request_token">Fetch request token</a></span>
        <a href="" class="show-code"><i>show code</i></a>
        <div class="code">
            <? highlight_file('request_token.php'); ?>
        </div>
    </div>
    <div class="step">
        <span><a href="" id="open_authorize_url">Open authorize url</a></span>
        <a href="" class="show-code"><i>show code</i></a>
        <div class="code">
            <? highlight_file('authorize_url.php'); ?>
        </div>
    </div>
    <div class="step">
        <span><a href="" id="fetch_access_token">Fetch access token</a></span>
        <a href="" class="show-code"><i>show code</i></a>
        <div class="code">
            <? highlight_file('access_token.php'); ?>
        </div>
    </div>
    <div class="step">
        <span><a href="" id="oauth_request">OAuth request</a></span>
        <a href="" class="show-code"><i>show code</i></a>
        <div class="code">
            <? highlight_file('oauth_request.php'); ?>
        </div>
    </div>
</div>
</body>
</html>
