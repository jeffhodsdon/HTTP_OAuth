<html>
<head>
    <title>HTTP_OAuth Example Tool</title>

    <style type="text/css" media="all">@import "styles.css";</style>
    <style type="text/css" media="all">@import "thickbox.css";</style>

    <script src="jquery.js" type="text/javascript"></script>
    <script src="jquery.cookie.js" type="text/javascript"></script>
    <script src="jquery.fade.js" type="text/javascript"></script>
    <script src="thickbox.js" type="text/javascript"></script>
</head>
<body>

<script type="text/javascript">
$(document).ready(function() {
    $('input').each(function() {
        if (this.value.length < 1) {
            this.value = $.cookie(this.id);
        }
    });

    $('.add-argument').click(function() {
        var add = $(this).siblings('div').eq(0).clone();
        add.children('input').val('');
        $(this).before(add);
        return false;
    });

    if ($('#verifier').val().length > 1) {
        activate_access_token();
        $('.code').hide();
        $('#authorize-url-code').show();
    } else {
        activate_request_token();
    }

    $('.into-html').click(function() {
        $(this).html($(this).text());
    });

    function activate_request_token() {
        $('#fetch_request_token').removeClass('disabled').click(function() {
            var data = {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                request_token_url: $('#request_token_url').val(),
                callback_url: window.location.toString(),
                method: $('#request_token_method').val()
            }

            if ($('#request_token_method').val() == 'POST') {
                method = 'POST';
                $('#request_token_arguments > div.argument').each(function() {
                    data['args[' + $(this).children('input').eq(0).val() + ']'] =
                        $(this).children('input').eq(1).val();
                });
            }

            $.ajax({
                type: "GET",
                url: "request_token.php",
                dataType: "json",
                data: data,
                success: function(json) {
                    if (json.token === undefined || json.token_secret === undefined) {
                        alert('Error!');
                    }

                    $('#token').val(json.token).vkfade();
                    $('#token_secret').val(json.token_secret).vkfade();
                    success('Got request token! ready to open authorize url');
                    activate_authorize();
                    $('.code').hide();
                    $('#request-token-code').show();
                    return false;
                },
                error: function(res) {
                    error(res.responseText);
                }
            });

            return false;
        });
    }

    function activate_authorize() {
        $('#open_authorize_url').removeClass('disabled').click(function() {
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
    }

    function activate_access_token() {
        $('#fetch_access_token').removeClass('disabled').click(function() {
            var data = {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                token: $('#token').val(),
                token_secret: $('#token_secret').val(),
                verifier: $('#verifier').val(),
                access_token_url: $('#access_token_url').val(),
                method: $('#access_token_method').val()
            }

            if ($('#access_token_method').val() == 'POST') {
                method = 'POST';
                $('#access_token_arguments > div.argument').each(function() {
                    data['args[' + $(this).children('input').eq(0).val() + ']'] =
                        $(this).children('input').eq(1).val();
                });
            }

            $.ajax({
                type: "GET",
                url: "access_token.php",
                dataType: "json",
                data: data,
                success: function(json) {
                    if (json.token === undefined || json.token_secret === undefined) {
                        alert('Error!');
                    }

                    $('#token').val(json.token).vkfade();
                    $('#token_secret').val(json.token_secret).vkfade();
                    success('Got access token! ready to do oauth requests');
                    activate_oauth_request();
                    $('.code').hide();
                    $('#access-token-code').show();
 
                    return false;
                },
                error: function(res) {
                    error(res.responseText);
                }
            });

            return false;
        });
    }

    function activate_oauth_request() {
        $('#oauth_request').removeClass('disabled').click(function() {
            var data = {
                consumer_key: $('#consumer_key').val(),
                consumer_secret: $('#consumer_secret').val(),
                token: $('#token').val(),
                token_secret: $('#token_secret').val(),
                protected_resource: $('#protected_resource_url').val(),
                method: $('#protected_resource_method').val()
            }

            if ($('#protected_resource_method').val() == 'POST') {
                method = 'POST';
                $('#protected_resource_arguments > div.argument').each(function() {
                    data['args[' + $(this).children('input').eq(0).val() + ']'] =
                        $(this).children('input').eq(1).val();
                });
            }


            $.ajax({
                type: "GET",
                url: "oauth_request.php",
                data: data,
                success: function(res) {
                    success(res);
                    $('.code').hide();
                    $('#oauth-request-code').show();

                    return false;
                },
                error: function(res) {
                    error(res.responseText);
                }
            });

            return false;
        });
    }

    $('.defaults > select').change(function() {
        switch ($(this).val()) {
        case 'digg':
            var request_token_url = 'http://services.digg.com/1.0/endpoint?method=oauth.getRequestToken';
            var authorize_url     = 'http://digg.com/oauth/authorize';
            var access_token_url  = 'http://services.digg.com/1.0/endpoint?method=oauth.getAccessToken';
            break;
        case 'twitter':
            var request_token_url = 'http://twitter.com/oauth/request_token';
            var authorize_url     = 'http://twitter.com/oauth/authorize';
            var access_token_url  = 'http://twitter.com/oauth/access_token';
            $('#protected_resource').val('http://twitter.com/account/verify_credentials.xml');
            break;
        case 'google':
            var request_token_url = 'https://www.google.com/accounts/OAuthGetRequestToken';
            var authorize_url     = 'https://www.google.com/accounts/OAuthAuthorizeToken';
            var access_token_url  = 'https://www.google.com/accounts/OAuthGetAccessToken';
            $('#protected_resource').val('http://www.google.com/calendar/feeds/default/allcalendars/full?orderby=starttime');
            break;
        case 'yahoo':
            var request_token_url = 'http://api.login.yahoo.com/oauth/v2/get_request_token';
            var authorize_url     = 'https://api.login.yahoo.com/oauth/v2/request_auth';
            var access_token_url  = 'https://api.login.yahoo.com/oauth/v2/get_token';
            $('#protected_resource').val('http://social.yahooapis.com/v1/user/abcdef123/profile?format=json');
            break;
        case 'myspace':
            var request_token_url = 'http://api.myspace.com/request_token';
            var authorize_url     = 'http://api.myspace.com/authorize';
            var access_token_url  = 'http://api.myspace.com/access_token';
            break;
        default:
            alert('Invalid default: ' + $(this).val());
            return false;
            break;
        }

        $('#request_token_url').val(request_token_url);
        $('#authorize_url').val(authorize_url);
        $('#access_token_url').val(access_token_url);

        return false;
    });

    $('.show-code').click(function() {
        $(this).siblings('.code').toggle('fast');
        return false;
    });

    function success(msg) {
        $('#success').html(msg).show('fast').vkfade();
        $('#error').hide('fast');
    }

    function error(msg) {
        $('#error').html(msg).show('fast').vkfade();
        $('#success').hide('fast');
    }

    $('.selector > span').click(function() {
        $(this).addClass('selected');
        $(this).siblings('.selected').removeClass('selected');
        $(this).siblings('input').val(this.innerHTML);
        if ($(this).siblings('input').val() == 'POST') {
            $(this).parent().siblings('.arguments').show();
        } else {
            $(this).parent().siblings('.arguments').hide();
        }
    });

});
</script>

<div class="settings">
    <form action="" id="settings-form">
    <h2>Settings</h2>
    <div class="defaults">
        <h3>Defaults: </h3>
        <select>
            <option value="myspace">MySpace</option>
            <option value="yahoo">Yahoo!</option>
            <option value="google">Google</option>
            <option value="twitter">Twitter</option>
            <option value="digg">Digg</option>
        </select>
    </div>
    <div class="clear"></div>

    <div class="setting">
        <label for="">Consumer Key</label>
        <input type="text" name="consumer_key" id="consumer_key">
    </div>
    <div class="setting">
        <label for="">Consumer Secret</label>
        <input type="text" name="consumer_secret" id="consumer_secret">
    </div>
    <div class="setting">
        <div class="selector">
            <span class="selected">GET</span>
            <span>POST</span>
            <input type="hidden" id="request_token_method" value="GET" />
        </div>
        <label for="">Request Token URL</label>
        <input type="text" name="request_token_url" id="request_token_url">
        <div class="clear"></div>
        <div style="display:none;" class="arguments" id="request_token_arguments">
            <div class="argument">
                <input type="text" class="argument" value="" id="" placeholder="name...">
                <input type="text" class="argument" value="" id="" placeholder="value...">
                <div class="clear"></div>
            </div>
            <a href="#" class="add-argument">Another argument...</a>
        </div>
    </div>
    <div class="setting">
        <label for="">Authorize URL</label>
        <input type="text" value="" id="authorize_url" name="authorize_url">
    </div>
    <div class="setting">
        <div class="selector">
            <span class="selected">GET</span>
            <span>POST</span>
            <input type="hidden" id="access_token_method" value="GET" />
        </div>
        <label for="">Access Token URL</label>
        <input type="text" name="access_token_url" id="access_token_url">
        <div class="clear"></div>
        <div style="display:none;" class="arguments" id="access_token_arguments">
            <div class="argument">
                <input type="text" class="argument" value="" id="" placeholder="name...">
                <input type="text" class="argument" value="" id="" placeholder="value...">
                <div class="clear"></div>
            </div>
            <a href="#" class="add-argument">Another argument...</a>
        </div>
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
        <div class="selector">
            <span class="selected">GET</span>
            <span>POST</span>
            <input type="hidden" id="protected_resource_method" value="GET" />
        </div>
        <label for="">Protected resource</label>
        <input type="text" name="protected_resource" id="protected_resource_url">
        <div class="clear"></div>
        <div style="display:none;" class="arguments" id="protected_resource_arguments">
            <div class="argument">
                <input type="text" class="argument" value="" id="" placeholder="name...">
                <input type="text" class="argument" value="" id="" placeholder="value...">
                <div class="clear"></div>
            </div>
            <a href="#" class="add-argument">Another argument...</a>
        </div>
        <div class="clear"></div>
    </div>
    </form>
</div>

<div id="error">
error
</div>

<div class="into-html" id="success">
success
</div>

<div class="example">
    <h2>OAuth Example</h2>
    <div class="clear"></div>
    <div class="step">
        <span><a id="fetch_request_token" class="disabled">Fetch request token</a></span>
        <span><a id="open_authorize_url" class="disabled">Open authorize url</a></span>
        <span><a id="fetch_access_token" class="disabled">Fetch access token</a></span>
        <span><a id="oauth_request" class="disabled">OAuth request</a></span>
    </div>
    <div class="code" id="request-token-code">
        <? highlight_file('request_token.php'); ?>
    </div>
    <div class="code" id="authorize-url-code">
        <? highlight_file('authorize_url.php'); ?>
    </div>
    <div class="code" id="access-token-code">
        <? highlight_file('access_token.php'); ?>
    </div>
    <div class="code" id="oauth-request-code">
        <? highlight_file('oauth_request.php'); ?>
    </div>
</div>


</body>
</html>
