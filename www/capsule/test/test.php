<?php
/**
 * Created by PhpStorm.
 * User: polyanin
 * Date: 16.10.2016
 * Time: 14:34
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', -1);
include dirname(__DIR__, 3) . '/capsule/src/Capsule/Capsule.php';
$system = \Capsule\Capsule::getInstance(dirname(__DIR__, 2)); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form method="post">
    <input type="text" name="<?=\Capsule\User\Auth::POST_VAR_USERNAME?>"><br>
    <input type="password" name="<?=\Capsule\User\Auth::POST_VAR_PASSWORD?>"><br>
    <input type="submit" value="ok">
</form>
</body>
</html>
<?php
//\Capsule\User\Auth::getInstance()->logout();

\Capsule\Tools\Tools::dump(\Capsule\User\Auth::getInstance()); ?>


