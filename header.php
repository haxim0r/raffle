<?php require 'php/session.php'; ?>
<?php require 'php/ajax.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <!--<link rel="icon" type="ICO" href="/images/favicon.ico">-->
        <link href="/css/000.css" rel="stylesheet"/>
        <script src="/js/global.js"></script>
        <?php if(isset($_SESSION['auth'])){ ?>
            <script>
                var wait = 420;
                var sessionTimer1, sessionTimer2;

                sessionTimer1 = setTimeout("alertUser()", (60000 * (wait - 1)));
                sessionTimer2 = setTimeout("logout()", 60000 * wait);
            </script>
        <?php } ?>
    </head>
    <body>
        <div id="timeoutPopup">Your session will expire in 1 minute unless there is activity.</div>
