<?php
    session_start();
    /*if($_SESSION['user_info']['role'] !== '3')
    {
        die('you do not have access to this, please click <a href="index.php">HERE</a>to return');
    }*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>C3 Intelligence, Inc.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="/css/instascreen.css">
    <link rel="stylesheet" type="text/css" href="/css/version2.css">
    <link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui-1.9.2.css">
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui-1.9.2-overrides.css">

    <link rel="stylesheet" type="text/css" href="/css/version2_5.css">
    <link rel="stylesheet" type="text/css" href="/css/is-bootstrap-overrides.css">
    <link rel="stylesheet" type="text/css" href="/css/is-layout.css">
    <link rel="stylesheet" type="text/css" href="/css/is-responsive.css">
    <link rel="stylesheet" type="text/css" href="/css/skin-default.css">
    <link rel="stylesheet" type="text/css" href="/css/theme.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-color-secondary.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/animate.css">
    <link rel="stylesheet" type="text/css" media="print" href="/css/is-printable.css">

    <link href="/css/googlefonts.css?family=Roboto:400,400italic,500,500italic,700,700italic" rel="stylesheet" type="text/css">
    <link href="/css/googlefonts_roboto_condensed.css?family=Roboto+Condensed:400,400italic,500,500italic,700,700italic" rel="stylesheet" type="text/css">
    <link href="/css/font-awesome.css" rel="stylesheet" type="text/css">


    <link rel="stylesheet" type="text/css" href="/css/da_net_skin.css">

    <script type="text/javascript" src="/js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="/js/prototype-1.7.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.9.2.js"></script>
    <script type="text/javascript" src="/js/jquery.corner.js"></script>
    <script type="text/javascript" src="/js/jquery.focus-first.custom.js"></script>
    <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/bootbox.min.js"></script>
    <script type="text/javascript" src="/js/skins.js"></script>
    <script type="text/javascript" src="/js/is-layout-version2_5.js"></script>
    <script type="text/javascript" src="/js/globalScripts.js"></script>
    <script type="text/javascript" src="/js/systemNotification.js"></script>
    <script type="text/javascript" src="/js/sessionExpireModal.js"></script>
    <script type="text/javascript" src="/js/handlebars-v3.0.3.js"></script>
    <script type="text/javascript" src="/js/jquery.feedback.custom.js"></script>
    <script type="text/javascript" src="/js/bootstrap-growl.min.js"></script>
    <script type="text/javascript">jQuery.noConflict();</script>

    <script type="text/javascript" src="/js/namespace.js"></script>
    <script type="text/javascript" src="/js/knockout-3.3.0.js"></script>
    <script type="text/javascript" src="/js/knockout.bindings.custom.js"></script>

    <script type="text/javascript" href="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/js/jquery.blockUI.js"></script>
</head>
<body>
<div class="well well-primary">
    <form role="form" action="route.php" method="post" autocomplete="off">
        <div class="form-group">
            <label for="user_name">
                <strong>User Name:</strong>
            </label>
            <span class="pull-right">
    </span>
            <input id="user_name" name="user_name" class="form-control" type="text" tabindex="2" maxlength="32">
        </div>

        <div class="form-group">
            <label for="password">
                <strong>Password:</strong>
            </label>
            <span class="pull-right">
    </span>
            <input id="password" name="password" class="form-control" type="password" tabindex="2" maxlength="32">
        </div>

        <div class="form-group">
            <label for="role">
                <strong>Role</strong>&nbsp;<small>1:user, 2:moderator, 3:Admin</small>
            </label>
            <span class="pull-right">
    </span>
            <input id="role" name="role" class="form-control" type="number" tabindex="3" maxlength="1">
        </div>

        <div class="text-center">
            <input type="submit" class="btn btn-primary" value="Register" tabindex="4">
        </div>
        <input type="hidden" id="token" name="token" value="3u4s649ekh4of0q372ppob2lhl9b06vdmoin">
        <input type="hidden" id="reg-form" name="formtype" value="reg-admin">
    </form>
</div>
</body>
</html>
