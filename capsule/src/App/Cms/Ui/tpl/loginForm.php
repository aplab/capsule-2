<?php 
use Capsule\User\Auth;
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="shortcut icon" href="/capsule/assets/cms/img/favicon.ico">
        <title>
            login
        </title>
    </head>
    <body>
        <form method="post" action="/admin/">
            <input type="text" name="<?=Auth::getInstance()->keyNameLogin?>"> <input type="password" name="<?=Auth::getInstance()->keyNamePassword?>">
            <button style="opacity:0;" type="submit"></button>
        </form>
    </body>
</html>