<?php
    session_start();
/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        12/10/2016
 * Time:        5:31 PM
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>C3 Intelligence, Inc. - Login</title>
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


    <link rel="stylesheet" type="text/css" href="/css/da_skin.css">

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

<body onload="onloadExecute()" onunload="onunloadExecute()">
<div id="login-container" class="container">

    <div class="row">
        <div id="login-spacer-lg" class="hidden-xs"></div>

        <!-- Company Logo -->
        <div class="col-sm-7 login-logo">
            <p class="text-center">
                <img src="showImage.taz&#x3f;alias=sample.com&amp;uid=4665" class="img-responsive">
            </p>
        </div>

        <!-- Login Form -->
        <div class="col-sm-5">
            <div class="well well-primary">
                <?php
                    if(isset($_SESSION['status']) && $_SESSION['status'] == 1){
                        echo '
                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span> Incorrect Login, Correct the issue and try again!</span>
                            </div>
                              ';
                    }
                ?>
                <script type="text/javascript">
                    jQuery(function ($) {
                        $('form').focusFirstVisible();
                    });
                </script>
                <form role="form" action="index.php" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="l-name">
                            <strong>Username:</strong>
                        </label>
                        <span class="pull-right">
      <small><a class="text-muted" href="reset.html">Forgot Username?</a></small>
    </span>
                        <input id="username" name="username" class="form-control" type="text" tabindex="1" maxlength="48" value="">
                    </div>
                    <div class="form-group">
                        <label for="l-pass">
                            <strong>Password:</strong>
                        </label>
                        <span class="pull-right">
      <small><a class="text-muted" href="reset.html">Forgot Password?</a></small>
    </span>
                        <input id="password" name="password" class="form-control" type="password" tabindex="2" maxlength="32">
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" value="Login" tabindex="4">
                    </div>
                    <input type="hidden" id="token" name="token" value="3u4s649ekh4of0q372ppob2lhl9b06vdmoin">
                    <input type="hidden" id="login-form" name="formtype" value="login-form">
                </form>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- System Notice -->
        <div class="col-xs-12">
            <div class="well">
                <strong>NOTICE:</strong> The use of this system is restricted.
                Only authorized users may access this system. All Access to this
                system is logged and regularly monitored for computer security purposes.
                Any unauthorized access to this system is prohibited and is subject to criminal
                and civil penalties under Federal Laws including, but not limited to, the Computer
                Fraud and Abuse Act and the National Information Infrastructure Protection Act.

            </div>
        </div>

        <!-- Company Links -->
        <div class="col-xs-12 text-center">
            <a href="http&#x3a;//www.sample.com">sample</a> &nbsp;
            <a href="http&#x3a;//www.sample.com">Homepage</a> &nbsp;

        </div>
    </div>

    <footer class="v-spacer text-center text-muted small">
        &copy; 2001-2016 &ndash; This Software Copyrighted &ndash; All Rights Reserved.
    </footer>
</div>
</body>
</html>